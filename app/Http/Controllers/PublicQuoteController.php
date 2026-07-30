<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;

class PublicQuoteController extends Controller
{
    public function show($token)
    {
        $quote = Quote::withoutGlobalScopes()->where('public_token', $token)->firstOrFail();
        
        // Ensure company is loaded to calculate subsidies properly in view if needed
        $quote->load('customer', 'items.product', 'company');
        
        return view('public.quotes.show', compact('quote'));
    }

    public function accept(Request $request, $token)
    {
        $quote = Quote::withoutGlobalScopes()->where('public_token', $token)->firstOrFail();

        // If it's already accepted, don't process again
        if ($quote->status === 'accepted') {
            return redirect()->route('public.quotes.show', $token)
                ->with('success', 'This quote has already been accepted.');
        }

        $request->validate([
            'signature' => 'required|string|max:255',
        ]);

        $quote->update([
            'status'         => 'accepted',
            'accepted_at'    => now(),
            'signature_data' => $request->signature,
            'client_ip'      => $request->ip(),
        ]);

        // Trigger post-acceptance automation logic (similar to QuoteController@update)
        if ($quote->lead) {
            $quote->lead->update(['stage' => 'won']);
        }
        if ($quote->customer && $quote->customer->status !== 'active') {
            $quote->customer->update(['status' => 'active']);
        }

        // Auto-schedule installation
        $exists = \App\Models\Installation::withoutGlobalScopes()->where('quote_id', $quote->id)->exists();
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

        return redirect()->route('public.quotes.show', $token)
            ->with('success', 'Thank you! You have successfully accepted the quote.');
    }
}
