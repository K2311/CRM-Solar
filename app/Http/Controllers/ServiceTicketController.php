<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Installation;
use App\Models\ServiceTicket;
use App\Models\User;
use Illuminate\Http\Request;

class ServiceTicketController extends Controller
{
    use \App\Traits\HasTenant;

    public function index(Request $request)
    {
        $query = ServiceTicket::with('customer', 'assignedUser');
        if ($request->status)   $query->where('status', $request->status);
        if ($request->priority) $query->where('priority', $request->priority);
        $tickets = $query->latest()->paginate(20)->withQueryString();
        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        $company = $this->tenantRequired();
        $customers     = Customer::orderBy('name')->get();
        $installations = Installation::with('customer')->get();
        $users         = User::where('company_id', $company->id)->get();
        return view('tickets.create', compact('customers', 'installations', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'     => 'required|exists:customers,id',
            'installation_id' => 'nullable|exists:installations,id',
            'assigned_user_id'=> 'nullable|exists:users,id',
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'priority'        => 'required|in:low,medium,high,urgent',
            'status'          => 'required|in:open,in_progress,resolved,closed',
        ]);
        ServiceTicket::create($data);
        return redirect()->route('tickets.index')->with('success', 'Ticket created.');
    }

    public function show(ServiceTicket $ticket)
    {
        $ticket->load('customer', 'installation', 'assignedUser', 'activities.user');
        return view('tickets.show', compact('ticket'));
    }

    public function edit(ServiceTicket $ticket)
    {
        $company = $this->tenantRequired();
        $customers     = Customer::orderBy('name')->get();
        $installations = Installation::with('customer')->get();
        $users         = User::where('company_id', $company->id)->get();
        return view('tickets.edit', compact('ticket', 'customers', 'installations', 'users'));
    }

    public function update(Request $request, ServiceTicket $ticket)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'priority'        => 'required|in:low,medium,high,urgent',
            'status'          => 'required|in:open,in_progress,resolved,closed',
            'assigned_user_id'=> 'nullable|exists:users,id',
        ]);
        if ($data['status'] === 'resolved' && !$ticket->resolved_at) {
            $data['resolved_at'] = now();
        }
        $ticket->update($data);
        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket updated.');
    }

    public function destroy(ServiceTicket $ticket)
    {
        $ticket->delete();
        return redirect()->route('tickets.index')->with('success', 'Ticket deleted.');
    }
}
