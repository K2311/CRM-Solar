<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Product;
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
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('leads.create', compact('customers', 'users', 'products'));
    }

    public function store(Request $request)
    {
        $company = $this->tenantRequired();
        if ($company->hasReachedLeadLimit()) {
            return back()->with('error', "Limit reached: Your subscription tier (" . strtoupper($company->plan) . ") allows a maximum of " . $company->plan_details['lead_limit'] . " leads. Please upgrade your plan.");
        }

        $data = $request->validate([
            'customer_type'      => 'required|in:new,existing',
            'customer_id'        => [
                'required_if:customer_type,existing',
                'nullable',
                \Illuminate\Validation\Rule::exists('customers', 'id')->where('company_id', $company->id)
            ],
            'new_customer_name'  => 'required_if:customer_type,new|nullable|string|max:255',
            'new_customer_email' => [
                \Illuminate\Validation\Rule::requiredIf(fn () => $request->customer_type === 'new' && empty($request->new_customer_phone)),
                'nullable', 
                'email', 
                'max:255',
                function ($attribute, $value, $fail) use ($request, $company) {
                    if ($request->customer_type === 'new' && $value) {
                        $exists = \App\Models\Customer::where('company_id', $company->id)
                            ->where('email', $value)
                            ->exists();
                        if ($exists) {
                            $fail('A customer with this email already exists. Please use the "Select Existing Customer" option.');
                        }
                    }
                },
            ],
            'new_customer_phone' => [
                'nullable',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($request, $company) {
                    if ($request->customer_type === 'new' && $value) {
                        $exists = \App\Models\Customer::where('company_id', $company->id)
                            ->where('phone', $value)
                            ->exists();
                        if ($exists) {
                            $fail('A customer with this phone number already exists. Please use the "Select Existing Customer" option.');
                        }
                    }
                },
            ],
            'title'              => 'nullable|string|max:255',
            'stage'              => 'required|in:new,contacted,survey_scheduled,quote_sent,negotiation,won,lost,junk',
            'source'             => 'nullable|string|max:100',
            'notes'              => 'nullable|string',
            'expected_close_date'=> 'nullable|date',
            'value'              => 'nullable|numeric|min:0',
            'assigned_user_id'   => [
                'nullable',
                \Illuminate\Validation\Rule::exists('users', 'id')->where('company_id', $company->id)
            ],
            'product_ids'        => 'nullable|array',
            'product_ids.*'      => [
                \Illuminate\Validation\Rule::exists('products', 'id')->where('company_id', $company->id)
            ],
        ], [
            'customer_id.required_if' => 'Please select an existing customer.',
            'new_customer_name.required_if' => 'Please provide a name for the new prospect.',
            'new_customer_email.required' => 'Please provide either an Email or a Phone number for the new prospect.',
        ]);

        $customerId = $request->customer_id;
        if (!$customerId && $request->filled('new_customer_name')) {
            $customer = Customer::create([
                'company_id' => $company->id,
                'name' => $request->new_customer_name,
                'email' => $request->new_customer_email,
                'phone' => $request->new_customer_phone,
                'source' => $request->source,
                'status' => 'prospect',
            ]);
            $customerId = $customer->id;
        }
        $data['customer_id'] = $customerId;
        $data['value'] = $data['value'] ?? 0;

        $lead = Lead::create($data);
        
        if ($request->has('product_ids')) {
            $lead->products()->sync($request->product_ids);
        }

        return redirect()->route('leads.index')->with('success', 'Lead created.');
    }

    public function show(Lead $lead)
    {
        $lead->load('customer', 'quotes', 'installation', 'activities.user', 'assignedUser', 'products');
        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $company = $this->tenantRequired();
        $customers = Customer::orderBy('name')->get();
        $users = User::where('company_id', $company->id)->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('leads.edit', compact('lead', 'customers', 'users', 'products'));
    }

    public function update(Request $request, Lead $lead)
    {
        $company = $this->tenantRequired();

        $data = $request->validate([
            'customer_type'      => 'required|in:new,existing',
            'customer_id'        => [
                'required_if:customer_type,existing',
                'nullable',
                \Illuminate\Validation\Rule::exists('customers', 'id')->where('company_id', $company->id)
            ],
            'new_customer_name'  => 'required_if:customer_type,new|nullable|string|max:255',
            'new_customer_email' => [
                \Illuminate\Validation\Rule::requiredIf(fn () => $request->customer_type === 'new' && empty($request->new_customer_phone)),
                'nullable', 
                'email', 
                'max:255',
                function ($attribute, $value, $fail) use ($request, $company) {
                    if ($request->customer_type === 'new' && $value) {
                        $exists = \App\Models\Customer::where('company_id', $company->id)
                            ->where('email', $value)
                            ->exists();
                        if ($exists) {
                            $fail('A customer with this email already exists. Please use the "Select Existing Customer" option.');
                        }
                    }
                },
            ],
            'new_customer_phone' => [
                'nullable',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($request, $company) {
                    if ($request->customer_type === 'new' && $value) {
                        $exists = \App\Models\Customer::where('company_id', $company->id)
                            ->where('phone', $value)
                            ->exists();
                        if ($exists) {
                            $fail('A customer with this phone number already exists. Please use the "Select Existing Customer" option.');
                        }
                    }
                },
            ],
            'title'              => 'nullable|string|max:255',
            'stage'              => 'required|in:new,contacted,survey_scheduled,quote_sent,negotiation,won,lost,junk',
            'source'             => 'nullable|string|max:100',
            'notes'              => 'nullable|string',
            'expected_close_date'=> 'nullable|date',
            'value'              => 'nullable|numeric|min:0',
            'assigned_user_id'   => [
                'nullable',
                \Illuminate\Validation\Rule::exists('users', 'id')->where('company_id', $company->id)
            ],
            'lost_reason'        => 'nullable|string',
            'product_ids'        => 'nullable|array',
            'product_ids.*'      => [
                \Illuminate\Validation\Rule::exists('products', 'id')->where('company_id', $company->id)
            ],
        ], [
            'customer_id.required_if' => 'Please select an existing customer.',
            'new_customer_name.required_if' => 'Please provide a name for the new prospect.',
            'new_customer_email.required' => 'Please provide either an Email or a Phone number for the new prospect.',
        ]);

        $customerId = $request->customer_id;
        if (!$customerId && $request->filled('new_customer_name')) {
            $customer = Customer::create([
                'company_id' => $company->id,
                'name' => $request->new_customer_name,
                'email' => $request->new_customer_email,
                'phone' => $request->new_customer_phone,
                'source' => $request->source,
                'status' => 'prospect',
            ]);
            $customerId = $customer->id;
        }
        $data['customer_id'] = $customerId;
        $data['value'] = $data['value'] ?? 0;

        $lead->update($data);
        
        if ($request->has('product_ids')) {
            $lead->products()->sync($request->product_ids);
        } else {
            $lead->products()->sync([]);
        }

        return redirect()->route('leads.show', $lead)->with('success', 'Lead updated.');
    }

    public function updateStage(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'stage' => 'required|in:new,contacted,survey_scheduled,quote_sent,negotiation,won,lost,junk',
            'lost_reason' => 'nullable|string'
        ]);

        $oldStage = $lead->stage;
        $lead->update([
            'stage' => $data['stage'],
            'lost_reason' => $data['lost_reason'] ?? $lead->lost_reason,
        ]);

        if ($data['stage'] === 'won' && $lead->customer) {
            $lead->customer->update(['status' => 'active']);
        }

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
