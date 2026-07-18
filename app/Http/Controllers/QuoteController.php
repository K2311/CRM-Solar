<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    use \App\Traits\HasTenant;

    public function index()
    {
        $quotes = Quote::with('customer', 'lead')->latest()->paginate(20);
        return view('quotes.index', compact('quotes'));
    }

    public function create(Request $request)
    {
        $customers = Customer::orderBy('name')->get();
        $leads     = Lead::with('customer')->whereNotIn('stage', ['won', 'lost'])->get();
        $products  = Product::where('is_active', true)->orderBy('name')->get();
        $lead      = $request->lead_id ? Lead::find($request->lead_id) : null;
        return view('quotes.create', compact('customers', 'leads', 'products', 'lead'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'lead_id'     => 'nullable|exists:leads,id',
            'valid_until' => 'nullable|date',
            'notes'       => 'nullable|string',
            'discount'    => 'nullable|numeric|min:0',
            'tax_rate'    => 'nullable|numeric|min:0|max:100',
            'has_subsidy' => 'nullable|in:0,1,on,off,true,false',
            'advance_milestone_pct' => 'nullable|numeric|min:0|max:100',
            'delivery_milestone_pct' => 'nullable|numeric|min:0|max:100',
            'commissioning_milestone_pct' => 'nullable|numeric|min:0|max:100',
            'items'            => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.qty'      => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.product_id' => 'nullable|exists:products,id',
        ]);

        $company = $this->tenantRequired();
        $hasSubsidy = $request->has('has_subsidy') && ($request->has_subsidy == '1' || $request->has_subsidy == 'on' || $request->has_subsidy == 'true');

        $quote = Quote::create([
            'company_id'   => $company->id,
            'customer_id'  => $data['customer_id'],
            'lead_id'      => $data['lead_id'] ?? null,
            'quote_number' => Quote::generateNumber($company->id),
            'valid_until'  => $data['valid_until'] ?? null,
            'notes'        => $data['notes'] ?? null,
            'discount'     => $data['discount'] ?? 0,
            'tax_rate'     => $data['tax_rate'] ?? 0,
            'has_subsidy'  => $hasSubsidy,
            'advance_milestone_pct' => $data['advance_milestone_pct'] ?? 10.00,
            'delivery_milestone_pct' => $data['delivery_milestone_pct'] ?? 70.00,
            'commissioning_milestone_pct' => $data['commissioning_milestone_pct'] ?? 20.00,
            'status'       => 'draft',
        ]);

        foreach ($request->items as $item) {
            QuoteItem::create([
                'quote_id'   => $quote->id,
                'product_id' => $item['product_id'] ?? null,
                'description'=> $item['description'],
                'qty'        => $item['qty'],
                'unit_price' => $item['unit_price'],
                'subtotal'   => $item['qty'] * $item['unit_price'],
            ]);
        }

        $quote->load('items');
        $quote->recalculate();

        return redirect()->route('quotes.show', $quote)->with('success', 'Quote created.');
    }

    public function show(Quote $quote)
    {
        $quote->load('customer', 'lead', 'items.product', 'payments');
        return view('quotes.show', compact('quote'));
    }

    public function edit(Quote $quote)
    {
        $customers = Customer::orderBy('name')->get();
        $leads     = Lead::with('customer')->get();
        $products  = Product::where('is_active', true)->orderBy('name')->get();
        $quote->load('items.product');
        return view('quotes.edit', compact('quote', 'customers', 'leads', 'products'));
    }

    public function update(Request $request, Quote $quote)
    {
        $data = $request->validate([
            'status'      => 'required|in:draft,sent,accepted,rejected',
            'valid_until' => 'nullable|date',
            'notes'       => 'nullable|string',
            'discount'    => 'nullable|numeric|min:0',
            'tax_rate'    => 'nullable|numeric|min:0|max:100',
            'has_subsidy' => 'nullable|in:0,1,on,off,true,false',
            'advance_milestone_pct' => 'nullable|numeric|min:0|max:100',
            'delivery_milestone_pct' => 'nullable|numeric|min:0|max:100',
            'commissioning_milestone_pct' => 'nullable|numeric|min:0|max:100',
        ]);

        $company = $this->tenantRequired();
        $hasSubsidy = $request->has('has_subsidy') && ($request->has_subsidy == '1' || $request->has_subsidy == 'on' || $request->has_subsidy == 'true');
        $data['has_subsidy'] = $hasSubsidy;

        $quote->update($data);
        $quote->load('items');
        $quote->recalculate();

        if ($data['status'] === 'accepted') {
            if ($quote->lead) {
                $quote->lead->update(['stage' => 'won']);
            }

            $exists = \App\Models\Installation::where('quote_id', $quote->id)->exists();
            if (!$exists) {
                $kw = 0.0;
                $panelBrand = null;
                $inverterBrand = null;
                $panelCount = 0;

                foreach ($quote->items as $item) {
                    if ($item->product) {
                        if ($item->product->category === 'panel') {
                            $kw += (($item->product->capacity_watts ?? 0) * $item->qty) / 1000.0;
                            $panelBrand = $item->product->brand;
                            $panelCount += $item->qty;
                        } elseif ($item->product->category === 'inverter') {
                            $inverterBrand = $item->product->brand;
                        }
                    }
                }

                $inst = \App\Models\Installation::create([
                    'company_id'      => $quote->company_id,
                    'customer_id'     => $quote->customer_id,
                    'lead_id'         => $quote->lead_id,
                    'quote_id'        => $quote->id,
                    'status'          => 'scheduled',
                    'scheduled_date'  => now()->addDays(7),
                    'system_size_kw'  => $kw,
                    'panel_brand'     => $panelBrand,
                    'inverter_brand'  => $inverterBrand,
                    'panel_count'     => $panelCount,
                ]);

                $defaultMilestones = \App\Models\InstallationMilestone::defaultMilestones();
                foreach ($defaultMilestones as $num => $name) {
                    \App\Models\InstallationMilestone::create([
                        'installation_id'  => $inst->id,
                        'milestone_number' => $num,
                        'name'             => $name,
                        'status'           => 'pending',
                    ]);
                }
            }
        }

        return redirect()->route('quotes.show', $quote)->with('success', 'Quote updated.');
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();
        return redirect()->route('quotes.index')->with('success', 'Quote deleted.');
    }

    public function send(Quote $quote)
    {
        $quote->update(['status' => 'sent']);
        return redirect()->route('quotes.show', $quote)->with('success', 'Quote sent to client.');
    }
}
