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
        $company = $this->tenantRequired();
        
        $data = $request->validate([
            'customer_id'    => [
                'required',
                \Illuminate\Validation\Rule::exists('customers', 'id')->where('company_id', $company->id)
            ],
            'lead_id'        => [
                'nullable',
                \Illuminate\Validation\Rule::exists('leads', 'id')->where('company_id', $company->id)
            ],
            'quote_id'       => [
                'nullable',
                \Illuminate\Validation\Rule::exists('quotes', 'id')->where('company_id', $company->id)
            ],
            'assigned_user_id' => [
                'nullable',
                \Illuminate\Validation\Rule::exists('users', 'id')->where('company_id', $company->id)
            ],
            'status'         => 'required|in:scheduled,in_progress,completed,cancelled',
            'scheduled_date' => 'nullable|date',
            'completed_date' => 'nullable|date',
            'system_size_kw' => 'nullable|numeric|min:0',
            'panel_brand'    => 'nullable|string|max:100',
            'inverter_brand' => 'nullable|string|max:100',
            'panel_count'    => 'nullable|integer|min:0',
            'notes'          => 'nullable|string',
        ]);
        $installation = Installation::create($data);
        
        // Auto-generate 10 default milestones
        foreach (\App\Models\InstallationMilestone::defaultMilestones() as $number => $name) {
            $installation->milestones()->create([
                'milestone_number' => $number,
                'name' => $name,
                'status' => 'pending'
            ]);
        }
        
        return redirect()->route('installations.index')->with('success', 'Installation created.');
    }

    public function show(Installation $installation)
    {
        $installation->load([
            'customer', 'lead', 'quote', 'assignedUser', 'serviceTickets', 'activities.user',
            'milestones' => fn($q) => $q->orderBy('milestone_number')
        ]);

        // Fix for existing installations that didn't get milestones generated
        if ($installation->milestones->isEmpty()) {
            foreach (\App\Models\InstallationMilestone::defaultMilestones() as $number => $name) {
                $installation->milestones()->create([
                    'milestone_number' => $number,
                    'name' => $name,
                    'status' => 'pending'
                ]);
            }
            $installation->load(['milestones' => fn($q) => $q->orderBy('milestone_number')]);
        }

        return view('installations.show', compact('installation'));
    }

    public function updateMilestone(Request $request, Installation $installation, \App\Models\InstallationMilestone $milestone)
    {
        abort_if($milestone->installation_id !== $installation->id, 404);

        $data = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
            'notes'  => 'nullable|string',
            'photo'  => 'nullable|image|max:5000',
        ]);

        $updateData = [
            'status' => $data['status'],
            'notes'  => $data['notes'] ?? null,
        ];

        // Enforce sequential milestone completion: all previous milestones must be completed
        if ($data['status'] === 'completed' && $milestone->milestone_number > 1) {
            $incompletePreviousMilestones = $installation->milestones()
                ->where('milestone_number', '<', $milestone->milestone_number)
                ->where('status', '!=', 'completed')
                ->count();
                
            if ($incompletePreviousMilestones > 0) {
                return redirect()->back()->with('error', 'You cannot complete this milestone because previous steps are still pending or in progress.');
            }
        }

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

            // Auto-generate GST Invoice with a transaction to prevent invoice number race conditions
            \Illuminate\Support\Facades\DB::transaction(function () use ($installation, $data) {
                $exists = \App\Models\GstInvoice::where('installation_id', $installation->id)->lockForUpdate()->exists();
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
            });
        }

        return redirect()->route('installations.show', $installation)->with('success', 'Milestone updated.');
    }

    public function edit(Installation $installation)
    {
        $company = $this->tenantRequired();
        $customers = Customer::orderBy('name')->get();
        $leads     = Lead::with('customer')->where('stage', 'won')->get();
        $quotes    = Quote::with('customer')->where('status', 'accepted')->get();
        $users     = User::where('company_id', $company->id)->get();
        return view('installations.edit', compact('installation', 'customers', 'leads', 'quotes', 'users'));
    }

    public function update(Request $request, Installation $installation)
    {
        $company = $this->tenantRequired();

        $data = $request->validate([
            'lead_id'        => [
                'nullable',
                \Illuminate\Validation\Rule::exists('leads', 'id')->where('company_id', $company->id)
            ],
            'quote_id'       => [
                'nullable',
                \Illuminate\Validation\Rule::exists('quotes', 'id')->where('company_id', $company->id)
            ],
            'status'         => 'required|in:scheduled,in_progress,completed,cancelled',
            'scheduled_date' => 'nullable|date',
            'completed_date' => 'nullable|date',
            'system_size_kw' => 'nullable|numeric',
            'panel_brand'    => 'nullable|string|max:100',
            'inverter_brand' => 'nullable|string|max:100',
            'panel_count'    => 'nullable|integer',
            'notes'          => 'nullable|string',
            'assigned_user_id' => [
                'nullable',
                \Illuminate\Validation\Rule::exists('users', 'id')->where('company_id', $company->id)
            ],
        ]);
        $installation->update($data);
        return redirect()->route('installations.show', $installation)->with('success', 'Installation updated.');
    }

    public function destroy(Installation $installation)
    {
        $installation->delete();
        return redirect()->route('installations.index')->with('success', 'Installation deleted.');
    }

    public function sendReminder(Installation $installation, \App\Services\Marketing\WhatsAppService $whatsAppService)
    {
        $this->tenantRequired();
        
        if (!$installation->assignedUser || !$installation->assignedUser->phone) {
            return redirect()->back()->with('error', 'Assigned user does not have a phone number configured.');
        }

        $now = \Carbon\Carbon::now()->startOfDay();
        $scheduled = \Carbon\Carbon::parse($installation->scheduled_date)->startOfDay();
        
        $customerName = $installation->customer ? ($installation->customer->first_name . ' ' . $installation->customer->last_name) : 'Unknown';
        $sysSize = $installation->system_size_kw ?? 'N/A';
        $dateStr = $scheduled->format('M d, Y');

        if ($scheduled->equalTo($now)) {
            $body = "Hi {$installation->assignedUser->name},\n\nReminder: You have an installation scheduled for TODAY!\n\nCustomer: {$customerName}\nSystem Size: {$sysSize} kW\n\nPlease arrive on time and update the milestone progress in the CRM.\n\nGood luck!";
        } elseif ($scheduled->equalTo($now->copy()->addDay())) {
            $body = "Hi {$installation->assignedUser->name},\n\nThis is a reminder that you have an installation scheduled for TOMORROW ({$dateStr}).\n\nCustomer: {$customerName}\nSystem Size: {$sysSize} kW\n\nPlease ensure you have all necessary materials prepared.";
        } else {
            $body = "Hi {$installation->assignedUser->name},\n\nReminder for your upcoming installation scheduled on {$dateStr}.\n\nCustomer: {$customerName}\nSystem Size: {$sysSize} kW\n\nPlease check the CRM for any new updates.";
        }

        try {
            $whatsAppService->send($installation->company, $installation->assignedUser->phone, $body);
            return redirect()->back()->with('success', 'Reminder sent successfully via WhatsApp.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send WhatsApp reminder: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send WhatsApp reminder.');
        }
    }
}
