<?php

namespace App\Http\Controllers;

use App\Models\PlanUpgradeRequest;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        $company = auth()->user()->company;
        
        // Fallback for super admin impersonation
        if (!$company && app()->has('current_company_id')) {
            $company = \App\Models\Company::find(app('current_company_id'));
        }

        $plans = config('plans');

        // Check if there's already a pending upgrade request
        $pendingRequest = $company
            ? PlanUpgradeRequest::where('company_id', $company->id)->where('status', 'pending')->first()
            : null;
        
        return view('billing.index', compact('company', 'plans', 'pendingRequest'));
    }

    public function upgrade(Request $request)
    {
        $request->validate([
            'plan'          => 'required|string',
            'payment_proof' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'notes'         => 'nullable|string|max:1000',
        ]);

        $company = auth()->user()->company;
        if (!$company && app()->has('current_company_id')) {
            $company = \App\Models\Company::find(app('current_company_id'));
        }

        if (!$company) {
            return back()->with('error', 'Company not found.');
        }

        // Check for existing pending request
        $exists = PlanUpgradeRequest::where('company_id', $company->id)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return back()->with('error', 'You already have a pending upgrade request. Please wait for admin review.');
        }

        // Store payment proof
        $proofPath = $request->file('payment_proof')->store('upgrade-proofs', 'public');

        PlanUpgradeRequest::create([
            'company_id'     => $company->id,
            'requested_by'   => auth()->id(),
            'current_plan'   => $company->plan,
            'requested_plan' => $request->plan,
            'payment_proof'  => $proofPath,
            'notes'          => $request->notes,
            'status'         => 'pending',
        ]);

        return redirect()->route('billing.index')
            ->with('success', 'Your upgrade request has been submitted! The admin will review your payment and activate the plan.');
    }
}
