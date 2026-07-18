<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Company;
use Illuminate\Http\Request;

class AdminPlanController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->is_super_admin, 403);
        $plans = Plan::latest()->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        abort_unless(auth()->user()->is_super_admin, 403);
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->is_super_admin, 403);

        $request->validate([
            'slug'               => 'required|string|max:50|unique:plans,slug',
            'name'               => 'required|string|max:255',
            'price'              => 'required|numeric|min:0',
            'user_limit'         => 'required|integer|min:1',
            'lead_limit'         => 'required|integer|min:1',
            'whatsapp_templates' => 'nullable|boolean',
            'branding'           => 'nullable|boolean',
        ]);

        Plan::create([
            'slug'               => $request->slug,
            'name'               => $request->name,
            'price'              => $request->price,
            'user_limit'         => $request->user_limit,
            'lead_limit'         => $request->lead_limit,
            'whatsapp_templates' => $request->has('whatsapp_templates'),
            'branding'           => $request->has('branding'),
        ]);

        return redirect()->route('admin.plans.index')->with('success', 'Plan created successfully.');
    }

    public function edit(Plan $plan)
    {
        abort_unless(auth()->user()->is_super_admin, 403);
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        abort_unless(auth()->user()->is_super_admin, 403);

        $request->validate([
            'name'               => 'required|string|max:255',
            'price'              => 'required|numeric|min:0',
            'user_limit'         => 'required|integer|min:1',
            'lead_limit'         => 'required|integer|min:1',
            'whatsapp_templates' => 'nullable|boolean',
            'branding'           => 'nullable|boolean',
        ]);

        $plan->update([
            'name'               => $request->name,
            'price'              => $request->price,
            'user_limit'         => $request->user_limit,
            'lead_limit'         => $request->lead_limit,
            'whatsapp_templates' => $request->has('whatsapp_templates'),
            'branding'           => $request->has('branding'),
        ]);

        return redirect()->route('admin.plans.index')->with('success', 'Plan updated successfully.');
    }

    public function destroy(Plan $plan)
    {
        abort_unless(auth()->user()->is_super_admin, 403);

        // Prevent deleting if currently active in any company
        $inUse = Company::where('plan', $plan->slug)->exists();
        if ($inUse) {
            return redirect()->route('admin.plans.index')->with('error', 'Cannot delete plan because it is currently assigned to one or more active companies.');
        }

        $plan->delete();
        return redirect()->route('admin.plans.index')->with('success', 'Plan deleted successfully.');
    }
}
