<x-app-layout title="View Quote #{{ $quote->quote_number }}">
    <style>
        @media print {
            .sidebar, .navbar, .no-print {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            .page-container {
                padding: 0 !important;
            }
            .card {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
            .avoid-break {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            table {
                page-break-inside: auto !important;
            }
            tr {
                page-break-inside: avoid !important;
                page-break-after: auto !important;
            }
        }
    </style>

    <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="{{ route('quotes.index') }}" class="btn btn-outline" style="padding: 0.5rem;"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 style="font-size: 1.5rem; font-weight: 800;">Proposal #{{ $quote->quote_number }}</h1>
                <span class="badge badge-info">{{ $quote->status }}</span>
            </div>
        </div>
        <div style="display: flex; gap: 1rem;">
            <button class="btn btn-outline" onclick="window.print()"><i class="bi bi-printer"></i> Print / PDF</button>
            @if(in_array($quote->status, ['sent', 'accepted']))
            <a href="{{ route('payments.create', ['quote_id' => $quote->id]) }}" class="btn btn-outline"><i class="bi bi-credit-card"></i> Record Payment</a>
            @endif
            @if(in_array($quote->status, ['draft', 'sent']))
            <form action="{{ route('quotes.send', $quote) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send"></i> 
                    {{ $quote->status === 'sent' ? 'Resend to Client' : 'Send to Client' }}
                </button>
            </form>
            @endif
        </div>
    </div>

    @if($quote->public_token)
    <div class="no-print" style="margin-bottom: 2rem; padding: 1.5rem; background: #e0f2fe; border: 1px solid #bae6fd; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div style="font-weight: 600; color: #0369a1; margin-bottom: 0.25rem;"><i class="bi bi-link-45deg"></i> Secure Public Link</div>
            <div style="font-size: 0.9rem; color: #0c4a6e;">Share this secure URL with your client so they can view and digitally accept this proposal.</div>
            <div style="margin-top: 0.75rem; background: #fff; padding: 0.5rem 1rem; border: 1px solid #7dd3fc; border-radius: 4px; font-family: monospace;">
                <a href="{{ route('public.quotes.show', $quote->public_token) }}" target="_blank" style="color: #0ea5e9; text-decoration: none;">
                    {{ route('public.quotes.show', $quote->public_token) }}
                </a>
            </div>
        </div>
        <button onclick="navigator.clipboard.writeText('{{ route('public.quotes.show', $quote->public_token) }}'); Swal.fire({toast:true, position:'top-end', icon:'success', title:'Link copied!', showConfirmButton:false, timer:2000});" class="btn btn-primary" style="background: #0284c7; border: none;"><i class="bi bi-clipboard"></i> Copy Link</button>
    </div>
    @endif

    <div class="card" style="padding: 3rem; background: #fff; color: #000; border: none; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
        <div style="display: flex; justify-content: space-between; margin-bottom: 4rem;">
            <div>
                <h2 style="font-weight: 800; color: var(--primary);">{{ $quote->company->name }}</h2>
                <p style="color: #666; font-size: 0.875rem; white-space: pre-line;">{{ $quote->company->address }}</p>
                <p style="color: #666; font-size: 0.875rem;">{{ $quote->company->email }} | {{ $quote->company->phone }}</p>
            </div>
            <div style="text-align: right;">
                <h2 style="font-weight: 800; color: #333; text-transform: uppercase;">Proposal</h2>
                <div style="margin-top: 1rem; display: flex; flex-direction: column; gap: 0.25rem;">
                    <div><span style="color: #999; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Quote Date:</span> <span style="font-weight: 600;">{{ $quote->created_at->format('M d, Y') }}</span></div>
                    <div><span style="color: #999; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Valid Until:</span> <span style="font-weight: 600;">{{ $quote->valid_until ? $quote->valid_until->format('M d, Y') : 'N/A' }}</span></div>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 3rem; display: flex; gap: 4rem; width: 100%;">
            <div style="flex: 1;">
                <h4 style="color: #999; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 1rem; border-bottom: 2px solid #eee; padding-bottom: 0.5rem;">Bill To</h4>
                <div style="font-size: 1.1rem; font-weight: 700;">{{ $quote->customer->name }}</div>
                @if($quote->customer->address)
                    <div style="color: #666; white-space: pre-line; margin-top: 0.5rem;">{{ $quote->customer->address }}</div>
                @endif
                @if($quote->customer->phone || $quote->customer->email)
                    <div style="color: #666; margin-top: 0.5rem;">
                        {{ $quote->customer->phone }}
                        @if($quote->customer->phone && $quote->customer->email) | @endif
                        {{ $quote->customer->email }}
                    </div>
                @endif
            </div>
            @if($quote->lead)
            <div style="flex: 1;">
                <h4 style="color: #999; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 1rem; border-bottom: 2px solid #eee; padding-bottom: 0.5rem;">Project Details</h4>
                <div style="font-size: 1rem; font-weight: 700;">{{ $quote->lead->title }}</div>
                <div style="color: #666; margin-top: 0.5rem; font-size: 0.875rem;">{{ $quote->lead->notes }}</div>
            </div>
            @else
            <div style="flex: 1;"></div>
            @endif
        </div>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 3rem;">
            <thead>
                <tr style="border-bottom: 2px solid #000;">
                    <th style="padding: 1rem 0; text-align: left;">Item Description</th>
                    <th style="padding: 1rem 0; text-align: center;">Qty</th>
                    <th style="padding: 1rem 0; text-align: right;">Unit Price</th>
                    <th style="padding: 1rem 0; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quote->items as $item)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 1.5rem 0;">
                        <div style="font-weight: 700;">{{ $item->product->name ?? 'Custom Component' }}</div>
                        <div style="font-size: 0.875rem; color: #666;">{{ $item->description }}</div>
                    </td>
                    <td style="padding: 1.5rem 0; text-align: center;">{{ $item->qty }}</td>
                    <td style="padding: 1.5rem 0; text-align: right;">{{ $currentCompany->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
                    <td style="padding: 1.5rem 0; text-align: right; font-weight: 700;">{{ $currentCompany->currency_symbol }}{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="avoid-break" style="display: flex; justify-content: flex-end;">
            <div style="width: 300px; display: flex; flex-direction: column; gap: 0.75rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #999; font-weight: 700; text-transform: uppercase; font-size: 0.75rem;">Subtotal</span>
                    <span style="font-weight: 600;">{{ $currentCompany->currency_symbol }}{{ number_format($quote->items->sum('subtotal'), 2) }}</span>
                </div>
                @if($quote->discount > 0)
                <div style="display: flex; justify-content: space-between; color: #ef4444;">
                    <span style="font-weight: 700; text-transform: uppercase; font-size: 0.75rem;">Discount</span>
                    <span style="font-weight: 600;">-{{ $currentCompany->currency_symbol }}{{ number_format($quote->discount, 2) }}</span>
                </div>
                @endif
                @if($quote->tax_rate > 0)
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #999; font-weight: 700; text-transform: uppercase; font-size: 0.75rem;">Tax ({{ $quote->tax_rate }}%)</span>
                    <span style="font-weight: 600;">+{{ $currentCompany->currency_symbol }}{{ number_format($quote->total - ($quote->items->sum('subtotal') - $quote->discount), 2) }}</span>
                </div>
                @endif
                <div style="display: flex; justify-content: space-between; padding-top: 1rem; border-top: 2px solid #000; font-size: 1.5rem;">
                    <span style="font-weight: 800;">TOTAL</span>
                    <span style="font-weight: 800; color: var(--primary);">{{ $currentCompany->currency_symbol }}{{ number_format($quote->total, 2) }}</span>
                </div>
                @if($quote->has_subsidy)
                <div style="display: flex; justify-content: space-between; color: #10b981; margin-top: 0.5rem;">
                    <span style="font-weight: 700; text-transform: uppercase; font-size: 0.75rem;">Central Subsidy</span>
                    <span style="font-weight: 600;">-{{ $currentCompany->currency_symbol }}{{ number_format($quote->central_subsidy, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; color: #10b981; margin-top: 0.25rem;">
                    <span style="font-weight: 700; text-transform: uppercase; font-size: 0.75rem;">State Subsidy</span>
                    <span style="font-weight: 600;">-{{ $currentCompany->currency_symbol }}{{ number_format($quote->state_subsidy, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding-top: 1rem; border-top: 2px dashed #eee; font-size: 1.5rem; color: #10b981; margin-top: 0.5rem;">
                    <span style="font-weight: 800;">NET COST</span>
                    <span style="font-weight: 800;">{{ $currentCompany->currency_symbol }}{{ number_format($quote->net_cost, 2) }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Payment Schedule -->
        <div class="avoid-break" style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid #eee;">
            <h4 style="color: #999; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 1rem;">Payment Milestone Schedule</h4>
            <div style="display: flex; gap: 2rem; background: #f9fafb; padding: 1.5rem; border-radius: 0.75rem;">
                <div style="flex: 1;">
                    <span style="color: #666; font-size: 0.8rem; display: block; margin-bottom: 0.25rem;">1. Booking Advance ({{ number_format($quote->advance_milestone_pct) }}%)</span>
                    <span style="font-size: 1.1rem; font-weight: 700;">{{ $currentCompany->currency_symbol }}{{ number_format(($quote->has_subsidy ? $quote->net_cost : $quote->total) * ($quote->advance_milestone_pct / 100), 2) }}</span>
                </div>
                <div style="flex: 1;">
                    <span style="color: #666; font-size: 0.8rem; display: block; margin-bottom: 0.25rem;">2. Material Delivery ({{ number_format($quote->delivery_milestone_pct) }}%)</span>
                    <span style="font-size: 1.1rem; font-weight: 700;">{{ $currentCompany->currency_symbol }}{{ number_format(($quote->has_subsidy ? $quote->net_cost : $quote->total) * ($quote->delivery_milestone_pct / 100), 2) }}</span>
                </div>
                <div style="flex: 1;">
                    <span style="color: #666; font-size: 0.8rem; display: block; margin-bottom: 0.25rem;">3. Commissioning ({{ number_format($quote->commissioning_milestone_pct) }}%)</span>
                    <span style="font-size: 1.1rem; font-weight: 700;">{{ $currentCompany->currency_symbol }}{{ number_format(($quote->has_subsidy ? $quote->net_cost : $quote->total) * ($quote->commissioning_milestone_pct / 100), 2) }}</span>
                </div>
            </div>
        </div>

        @if($quote->notes)
        <div class="avoid-break" style="margin-top: 4rem; padding: 2rem; background: #f9fafb; border-radius: 1rem; font-size: 0.875rem;">
            <h4 style="color: #999; font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem; font-size: 0.75rem;">Terms & Conditions</h4>
            <div style="color: #666; line-height: 1.6;">{{ $quote->notes }}</div>
        </div>
        @endif
    </div>
</x-app-layout>
