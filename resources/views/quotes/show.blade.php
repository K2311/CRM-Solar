<x-app-layout title="View Quote #{{ $quote->quote_number }}">
    <style>
        @media print {
            .sidebar, .navbar, .no-print {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
                padding: 0 !important;
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
            @if($quote->status === 'draft')
            <form action="{{ route('quotes.send', $quote) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Send to Client</button>
            </form>
            @endif
        </div>
    </div>

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

        <div style="margin-bottom: 3rem; display: grid; grid-template-columns: 1fr 1fr; gap: 4rem;">
            <div>
                <h4 style="color: #999; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 1rem; border-bottom: 2px solid #eee;">Bill To</h4>
                <div style="font-size: 1.1rem; font-weight: 700;">{{ $quote->customer->name }}</div>
                <div style="color: #666; white-space: pre-line; margin-top: 0.5rem;">{{ $quote->customer->address }}</div>
                <div style="color: #666;">{{ $quote->customer->phone }} | {{ $quote->customer->email }}</div>
            </div>
            @if($quote->lead)
            <div>
                <h4 style="color: #999; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 1rem; border-bottom: 2px solid #eee;">Project Details</h4>
                <div style="font-size: 1rem; font-weight: 700;">{{ $quote->lead->title }}</div>
                <div style="color: #666; margin-top: 0.5rem; font-size: 0.875rem;">{{ $quote->lead->notes }}</div>
            </div>
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

        <div style="display: flex; justify-content: flex-end;">
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
            </div>
        </div>

        @if($quote->notes)
        <div style="margin-top: 4rem; padding: 2rem; background: #f9fafb; border-radius: 1rem; font-size: 0.875rem;">
            <h4 style="color: #999; font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem; font-size: 0.75rem;">Terms & Conditions</h4>
            <div style="color: #666; line-height: 1.6;">{{ $quote->notes }}</div>
        </div>
        @endif
    </div>
</x-app-layout>
