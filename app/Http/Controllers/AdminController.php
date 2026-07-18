<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\PlanUpgradeRequest;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function companies()
    {
        abort_unless(auth()->user()->is_super_admin, 403);
        $companies = Company::withCount('users')->with('owner')->latest()->paginate(20);
        return view('admin.companies.index', compact('companies'));
    }

    public function impersonate(Company $company)
    {
        abort_unless(auth()->user()->is_super_admin, 403);
        session(['impersonate_company_id' => $company->id]);
        return redirect()->route('dashboard')->with('success', "Now managing {$company->name}");
    }

    public function stopImpersonating()
    {
        session()->forget('impersonate_company_id');
        return redirect()->route('admin.companies')->with('success', "Returned to super-admin view.");
    }

    public function storeCompany(Request $request)
    {
        abort_unless(auth()->user()->is_super_admin, 403);
        
        $request->validate([
            'company_name' => 'required|string|max:255',
            'email'        => 'nullable|email',
            'owner_name'   => 'required|string|max:255',
            'owner_email'  => 'required|email|unique:users,email',
            'password'     => 'required|string|min:8',
        ]);

        $company = Company::create([
            'name'            => $request->company_name,
            'slug'            => \Str::slug($request->company_name) . '-' . \Str::random(4),
            'email'           => $request->email,
            'timezone'        => 'UTC',
            'currency'        => 'USD',
            'plan'            => 'demo',
            'plan_status'     => 'active',
            'plan_expires_at' => now()->addDays(14),
        ]);

        \App\Models\User::create([
            'company_id' => $company->id,
            'name'       => $request->owner_name,
            'email'      => $request->owner_email,
            'password'   => \Illuminate\Support\Facades\Hash::make($request->password),
            'role'       => 'owner',
            'is_active'  => true,
        ]);

        // Seed default roles/permissions for the new company
        $allPerms = \App\Models\Permission::all();
        foreach ($allPerms as $perm) {
            // Admin gets everything
            \App\Models\RolePermission::create([
                'company_id' => $company->id,
                'role' => 'admin',
                'permission_id' => $perm->id,
                'granted' => true,
            ]);

            // Member default view
            if (\Str::endsWith($perm->name, '.view')) {
                \App\Models\RolePermission::create([
                    'company_id' => $company->id,
                    'role' => 'member',
                    'permission_id' => $perm->id,
                    'granted' => true,
                ]);
            }
            
            // Sales default
            $isSales = \Str::startsWith($perm->name, ['customers', 'leads', 'quotes']) || $perm->name === 'products.view';
            if ($isSales) {
                \App\Models\RolePermission::create([
                    'company_id' => $company->id,
                    'role' => 'sales',
                    'permission_id' => $perm->id,
                    'granted' => true,
                ]);
            }

            // Tech default
            $isTech = $perm->name === 'installations.view' || $perm->name === 'installations.edit' || \Str::startsWith($perm->name, 'tickets') || $perm->name === 'products.view';
            if ($isTech) {
                \App\Models\RolePermission::create([
                    'company_id' => $company->id,
                    'role' => 'technician',
                    'permission_id' => $perm->id,
                    'granted' => true,
                ]);
            }

            // Accounts default
            $isAccts = \Str::startsWith($perm->name, 'payments') || $perm->name === 'quotes.view' || $perm->name === 'products.view';
            if ($isAccts) {
                \App\Models\RolePermission::create([
                    'company_id' => $company->id,
                    'role' => 'accounts',
                    'permission_id' => $perm->id,
                    'granted' => true,
                ]);
            }
        }

        return back()->with('success', "Registered new company {$company->name} with Owner {$request->owner_name}");
    }

    public function updatePlan(Request $request, Company $company)
    {
        abort_unless(auth()->user()->is_super_admin, 403);

        $planSlugs = \App\Models\Plan::pluck('slug')->toArray();
        if (empty($planSlugs)) {
            $planSlugs = ['demo', 'pro', 'enterprise'];
        }

        $request->validate([
            'plan'            => 'required|in:' . implode(',', $planSlugs),
            'plan_status'     => 'required|in:active,suspended,expired',
            'plan_expires_at' => 'nullable|date',
        ]);

        $company->update([
            'plan'            => $request->plan,
            'plan_status'     => $request->plan_status,
            'plan_expires_at' => $request->plan_expires_at,
        ]);

        return back()->with('success', "Subscription settings updated for {$company->name}.");
    }

    public function upgradeRequests()
    {
        abort_unless(auth()->user()->is_super_admin, 403);
        $requests = PlanUpgradeRequest::with('company', 'requester', 'reviewer')
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->latest()
            ->paginate(20);
        return view('admin.upgrade-requests.index', compact('requests'));
    }

    public function approveRequest(PlanUpgradeRequest $upgradeRequest)
    {
        abort_unless(auth()->user()->is_super_admin, 403);

        if ($upgradeRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        // Activate the new plan on the company
        $upgradeRequest->company->update([
            'plan'            => $upgradeRequest->requested_plan,
            'plan_status'     => 'active',
            'plan_expires_at' => now()->addMonth(),
        ]);

        $upgradeRequest->update([
            'status'       => 'approved',
            'reviewed_by'  => auth()->id(),
            'reviewed_at'  => now(),
        ]);

        return back()->with('success', "Upgrade approved! {$upgradeRequest->company->name} is now on the {$upgradeRequest->requested_plan} plan.");
    }

    public function rejectRequest(Request $request, PlanUpgradeRequest $upgradeRequest)
    {
        abort_unless(auth()->user()->is_super_admin, 403);

        if ($upgradeRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $request->validate(['admin_remarks' => 'nullable|string|max:500']);

        $upgradeRequest->update([
            'status'        => 'rejected',
            'admin_remarks' => $request->admin_remarks,
            'reviewed_by'   => auth()->id(),
            'reviewed_at'   => now(),
        ]);

        return back()->with('success', "Upgrade request rejected for {$upgradeRequest->company->name}.");
    }
}
