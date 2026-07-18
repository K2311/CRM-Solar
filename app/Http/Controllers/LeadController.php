<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    use \App\Traits\HasTenant;

    public function index(Request $request)
    {
        $query = Lead::with('customer', 'assignedUser');
        if ($request->search) {
            $query->whereHas('customer', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }
        if ($request->stage) $query->where('stage', $request->stage);
        if ($request->assigned) $query->where('assigned_user_id', $request->assigned);

        if ($request->view === 'kanban') {
            $leadsByStage = [];
            foreach (Lead::stages() as $stage) {
                $leadsByStage[$stage] = (clone $query)->where('stage', $stage)->get();
            }
            return view('leads.kanban', compact('leadsByStage'));
        }

        $company = $this->tenantRequired();
        $leads = $query->latest()->paginate(20)->withQueryString();
        $users = User::where('company_id', $company->id)->get();
        return view('leads.index', compact('leads', 'users'));
    }

    public function create()
    {
        $company = $this->tenantRequired();
        $customers = Customer::orderBy('name')->get();
        $users = User::where('company_id', $company->id)->get();
        return view('leads.create', compact('customers', 'users'));
    }

    public function store(Request $request)
    {
        $company = $this->tenantRequired();
        if ($company->hasReachedLeadLimit()) {
            return back()->with('error', "Limit reached: Your subscription tier (" . strtoupper($company->plan) . ") allows a maximum of " . $company->plan_details['lead_limit'] . " leads. Please upgrade your plan.");
        }

        $data = $request->validate([
            'customer_id'        => 'required|exists:customers,id',
            'title'              => 'nullable|string|max:255',
            'stage'              => 'required|in:new,contacted,survey_scheduled,quote_sent,negotiation,won,lost,junk',
            'source'             => 'nullable|string|max:100',
            'notes'              => 'nullable|string',
            'expected_close_date'=> 'nullable|date',
            'value'              => 'nullable|numeric|min:0',
            'assigned_user_id'   => 'nullable|exists:users,id',
        ]);
        Lead::create($data);
        return redirect()->route('leads.index')->with('success', 'Lead created.');
    }

    public function show(Lead $lead)
    {
        $lead->load('customer', 'quotes', 'installation', 'activities.user', 'assignedUser');
        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $company = $this->tenantRequired();
        $customers = Customer::orderBy('name')->get();
        $users = User::where('company_id', $company->id)->get();
        return view('leads.edit', compact('lead', 'customers', 'users'));
    }

    public function update(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'customer_id'        => 'required|exists:customers,id',
            'title'              => 'nullable|string|max:255',
            'stage'              => 'required|in:new,contacted,survey_scheduled,quote_sent,negotiation,won,lost,junk',
            'source'             => 'nullable|string|max:100',
            'notes'              => 'nullable|string',
            'expected_close_date'=> 'nullable|date',
            'value'              => 'nullable|numeric|min:0',
            'assigned_user_id'   => 'nullable|exists:users,id',
            'lost_reason'        => 'nullable|string',
        ]);
        $lead->update($data);
        return redirect()->route('leads.show', $lead)->with('success', 'Lead updated.');
    }

    public function updateStage(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'stage' => 'required|in:new,contacted,survey_scheduled,quote_sent,negotiation,won,lost,junk',
        ]);

        $oldStage = $lead->stage;
        $lead->update(['stage' => $data['stage']]);

        \App\Models\Activity::create([
            'company_id'   => $lead->company_id,
            'user_id'      => auth()->id(),
            'subject_type' => Lead::class,
            'subject_id'   => $lead->id,
            'type'         => 'note',
            'description'  => "Stage updated from " . strtoupper($oldStage) . " to " . strtoupper($lead->stage),
        ]);

        return back()->with('success', 'Lead stage updated.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('leads.index')->with('success', 'Lead deleted.');
    }
}
