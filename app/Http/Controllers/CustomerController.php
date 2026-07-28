<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use \App\Traits\HasTenant;
    public function index(Request $request)
    {
        $query = Customer::query();
        if ($request->has('search')) {
            $search = "%{$request->search}%";
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('email', 'like', $search)
                  ->orWhere('phone', 'like', $search);
            });
        }
        if ($request->status) $query->where('status', $request->status);
        $customers = $query->latest()->paginate(20)->withQueryString();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $company = $this->tenantRequired();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required_without:phone',
                'nullable',
                'email',
                \Illuminate\Validation\Rule::unique('customers')->where('company_id', $company->id)
            ],
            'phone' => [
                'required_without:email',
                'nullable',
                'string',
                'max:30',
                \Illuminate\Validation\Rule::unique('customers')->where('company_id', $company->id)
            ],
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
            'source' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'status' => 'required|in:prospect,active,inactive',
        ], [
            'email.required_without' => 'Please provide either an Email Address or a Phone Number.',
            'phone.required_without' => 'Please provide either an Email Address or a Phone Number.',
            'email.unique' => 'A customer with this email already exists.',
            'phone.unique' => 'A customer with this phone number already exists.',
        ]);
        Customer::create($data);
        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['leads.quotes', 'installations', 'serviceTickets', 'payments', 'activities.user']);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $company = $this->tenantRequired();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required_without:phone',
                'nullable',
                'email',
                \Illuminate\Validation\Rule::unique('customers')
                    ->where('company_id', $company->id)
                    ->ignore($customer->id)
            ],
            'phone' => [
                'required_without:email',
                'nullable',
                'string',
                'max:30',
                \Illuminate\Validation\Rule::unique('customers')
                    ->where('company_id', $company->id)
                    ->ignore($customer->id)
            ],
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
            'source' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'status' => 'required|in:prospect,active,inactive',
        ], [
            'email.required_without' => 'Please provide either an Email Address or a Phone Number.',
            'phone.required_without' => 'Please provide either an Email Address or a Phone Number.',
            'email.unique' => 'A customer with this email already exists.',
            'phone.unique' => 'A customer with this phone number already exists.',
        ]);
        $customer->update($data);
        return redirect()->route('customers.show', $customer)->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        $leads = $customer->leads()->count();
        $quotes = $customer->quotes()->count();
        $installations = $customer->installations()->count();
        $serviceTickets = $customer->serviceTickets()->count();

        if ($leads || $quotes || $installations || $serviceTickets) {
            return redirect()->route('customers.show', $customer)
                ->with('error', "Cannot delete customer. This customer has {$leads} Leads, {$quotes} Quotes, {$installations} Installations, and {$serviceTickets} Service Tickets. Please delete these records first.");
        }

        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted.');
    }
}
