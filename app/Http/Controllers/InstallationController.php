<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Installation;
use App\Models\Lead;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Http\Request;

class InstallationController extends Controller
{
    use \App\Traits\HasTenant;

    public function index(Request $request)
    {
        $query = Installation::with('customer', 'assignedUser', 'quote');
        if ($request->status) $query->where('status', $request->status);
        $installations = $query->latest()->paginate(20)->withQueryString();
        return view('installations.index', compact('installations'));
    }

    public function create()
    {
        $company = $this->tenantRequired();
        $customers = Customer::orderBy('name')->get();
        $leads     = Lead::with('customer')->where('stage', 'won')->get();
        $quotes    = Quote::with('customer')->where('status', 'accepted')->get();
        $users     = User::where('company_id', $company->id)->get();
        return view('installations.create', compact('customers', 'leads', 'quotes', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'lead_id'        => 'nullable|exists:leads,id',
            'quote_id'       => 'nullable|exists:quotes,id',
            'assigned_user_id' => 'nullable|exists:users,id',
            'status'         => 'required|in:scheduled,in_progress,completed,cancelled',
            'scheduled_date' => 'nullable|date',
            'completed_date' => 'nullable|date',
            'system_size_kw' => 'nullable|numeric|min:0',
            'panel_brand'    => 'nullable|string|max:100',
            'inverter_brand' => 'nullable|string|max:100',
            'panel_count'    => 'nullable|integer|min:0',
            'notes'          => 'nullable|string',
        ]);
        Installation::create($data);
        return redirect()->route('installations.index')->with('success', 'Installation created.');
    }

    public function show(Installation $installation)
    {
        $installation->load('customer', 'lead', 'quote', 'assignedUser', 'serviceTickets', 'activities.user');
        return view('installations.show', compact('installation'));
    }

    public function edit(Installation $installation)
    {
        $company = $this->tenantRequired();
        $customers = Customer::orderBy('name')->get();
        $users     = User::where('company_id', $company->id)->get();
        return view('installations.edit', compact('installation', 'customers', 'users'));
    }

    public function update(Request $request, Installation $installation)
    {
        $data = $request->validate([
            'status'         => 'required|in:scheduled,in_progress,completed,cancelled',
            'scheduled_date' => 'nullable|date',
            'completed_date' => 'nullable|date',
            'system_size_kw' => 'nullable|numeric',
            'panel_brand'    => 'nullable|string|max:100',
            'inverter_brand' => 'nullable|string|max:100',
            'panel_count'    => 'nullable|integer',
            'notes'          => 'nullable|string',
            'assigned_user_id' => 'nullable|exists:users,id',
        ]);
        $installation->update($data);
        return redirect()->route('installations.show', $installation)->with('success', 'Installation updated.');
    }

    public function destroy(Installation $installation)
    {
        $installation->delete();
        return redirect()->route('installations.index')->with('success', 'Installation deleted.');
    }
}
