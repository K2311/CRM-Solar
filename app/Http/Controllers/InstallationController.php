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
        $installation->load([
            'customer', 'lead', 'quote', 'assignedUser', 'serviceTickets', 'activities.user',
            'milestones' => fn($q) => $q->orderBy('milestone_number')
        ]);
        return view('installations.show', compact('installation'));
    }

    public function updateMilestone(Request $request, Installation $installation, \App\Models\InstallationMilestone $milestone)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
            'notes'  => 'nullable|string',
            'photo'  => 'nullable|image|max:5000',
        ]);

        $updateData = [
            'status' => $data['status'],
            'notes'  => $data['notes'] ?? null,
        ];

        if ($data['status'] === 'completed') {
            $updateData['completed_at'] = now();
        } else {
            $updateData['completed_at'] = null;
        }

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('milestones', 'public');
            $updateData['photo_path'] = $path;
        }

        $milestone->update($updateData);

        // Milestone 9 Commissioning updates the installation to completed and creates GST invoice
        if ($milestone->milestone_number == 9 && $data['status'] === 'completed') {
            $installation->update([
                'status'         => 'completed',
                'completed_date' => now(),
            ]);

            // Auto-generate GST Invoice
            $exists = \App\Models\GstInvoice::where('installation_id', $installation->id)->exists();
            if (!$exists && $installation->quote) {
                $quote = $installation->quote;
                $subtotal = $quote->subtotal;
                $discount = $quote->discount;
                $taxableValue = $subtotal - $discount;
                
                $cgstRate = $quote->tax_rate / 2.0;
                $cgstAmount = $taxableValue * ($cgstRate / 100);
                $sgstRate = $quote->tax_rate / 2.0;
                $sgstAmount = $taxableValue * ($sgstRate / 100);
                
                \App\Models\GstInvoice::create([
                    'company_id'      => $installation->company_id,
                    'customer_id'     => $installation->customer_id,
                    'quote_id'        => $quote->id,
                    'installation_id' => $installation->id,
                    'invoice_number'  => \App\Models\GstInvoice::generateNumber($installation->company_id),
                    'invoice_date'    => now(),
                    'subtotal'        => $subtotal,
                    'discount'        => $discount,
                    'taxable_value'   => $taxableValue,
                    'cgst_rate'       => $cgstRate,
                    'cgst_amount'     => $cgstAmount,
                    'sgst_rate'       => $sgstRate,
                    'sgst_amount'     => $sgstAmount,
                    'total_gst'       => $cgstAmount + $sgstAmount,
                    'grand_total'     => $quote->total,
                    'status'          => 'unpaid',
                ]);
            }
        }

        return redirect()->route('installations.show', $installation)->with('success', 'Milestone updated.');
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
