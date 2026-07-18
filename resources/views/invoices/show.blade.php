<x-app-layout title="Tax Invoice #{{ $invoice->invoice_number }}">
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
            <a href="{{ route('invoices.index') }}" class="btn btn-outline" style="padding: 0.5rem;"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 style="font-size: 1.5rem; font-weight: 800;">Tax Invoice #{{ $invoice->invoice_number }}</h1>
                <span class="badge" style="background: {{ $invoice->status === 'paid' ? '#10b981' : '#f59e0b' }}">{{ strtoupper($invoice->status) }}</span>
            </div>
        </div>
        <div style="display: flex; gap: 1rem;">
            <button class="btn btn-outline" onclick="window.print()"><i class="bi bi-printer"></i> Print Invoice</button>
            <form action="{{ route('invoices.status', $invoice) }}" method="POST">
                @csrf
                @method('PATCH')
                @if($invoice->status === 'unpaid')
                <input type="hidden" name="status" value="paid">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Mark as Paid</button>
                @else
                <input type="hidden" name="status" value="unpaid">
                <button type="submit" class="btn btn-outline" style="border-color: #ef4444; color: #ef4444;"><i class="bi bi-x-lg"></i> Mark as Unpaid</button>
                @endif
            </form>
        </div>
    </div>

    <div class="card" style="padding: 3rem; background: #fff; color: #000; border: none; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); max-width: 800px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 3rem; border-bottom: 2px solid #eee; padding-bottom: 2rem;">
            <div>
                <h2 style="font-weight: 800; color: var(--primary); margin: 0 0 0.5rem 0;">{{ $invoice->company->name }}</h2>
                <p style="color: #555; font-size: 0.85rem; margin: 0 0 0.25rem 0; white-space: pre-line;">{{ $invoice->company->address }}</p>
                <p style="color: #555; font-size: 0.85rem; margin: 0;">Email: {{ $invoice->company->email }} | Phone: {{ $invoice->company->phone }}</p>
                <p style="font-size: 0.85rem; font-weight: 700; margin-top: 0.5rem; color: #333;">GSTIN: {{ $invoice->company->setting('company_gstin', '27AAAAA1111A1Z1') }}</p>
            </div>
            <div style="text-align: right;">
                <h2 style="font-weight: 800; color: #333; text-transform: uppercase; margin: 0 0 1rem 0; letter-spacing: 0.05em;">Tax Invoice</h2>
                <div style="display: flex; flex-direction: column; gap: 0.35rem; font-size: 0.85rem;">
                    <div><span style="color: #777; font-weight: 700; text-transform: uppercase;">Invoice #</span> <span style="font-weight: 700; color: #000;">{{ $invoice->invoice_number }}</span></div>
                    <div><span style="color: #777; font-weight: 700; text-transform: uppercase;">Invoice Date:</span> <span style="font-weight: 600;">{{ $invoice->invoice_date->format('M d, Y') }}</span></div>
                    <div><span style="color: #777; font-weight: 700; text-transform: uppercase;">State Code:</span> <span style="font-weight: 600;">{{ $invoice->company->setting('company_state_code', '27 (MH)') }}</span></div>
                </div>
            </div>
        </div>

        <div style="margin-bottom: 3rem; display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;">
            <div>
                <h4 style="color: #777; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin: 0 0 0.75rem 0; border-bottom: 2px solid #eee; padding-bottom: 0.25rem;">Recipient (Bill To)</h4>
                <div style="font-size: 1rem; font-weight: 700; color: #000;">{{ $invoice->customer->name }}</div>
                <div style="color: #555; white-space: pre-line; margin-top: 0.5rem; font-size: 0.85rem;">{{ $invoice->customer->address }}</div>
                <div style="color: #555; font-size: 0.85rem; margin-top: 0.25rem;">Phone: {{ $invoice->customer->phone }} | Email: {{ $invoice->customer->email }}</div>
                @if($invoice->customer->setting)
                <div style="font-size: 0.85rem; font-weight: 700; margin-top: 0.5rem;">GSTIN: {{ $invoice->customer->setting('customer_gstin', 'N/A') }}</div>
                @endif
            </div>
            <div>
                <h4 style="color: #777; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin: 0 0 0.75rem 0; border-bottom: 2px solid #eee; padding-bottom: 0.25rem;">Solar Installation Specs</h4>
                <div style="display: flex; flex-direction: column; gap: 0.4rem; font-size: 0.85rem;">
                    @if($invoice->installation)
                    <div><span style="color: #666;">System Size:</span> <span style="font-weight: 700;">{{ $invoice->installation->system_size_kw }} kW</span></div>
                    <div><span style="color: #666;">Panel Brand:</span> <span style="font-weight: 600;">{{ $invoice->installation->panel_brand }}</span></div>
                    <div><span style="color: #666;">Inverter Brand:</span> <span style="font-weight: 600;">{{ $invoice->installation->inverter_brand }}</span></div>
                    @endif
                </div>
            </div>
        </div>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 3rem; font-size: 0.875rem;">
            <thead>
                <tr style="border-bottom: 2px solid #000; font-weight: 700;">
                    <th style="padding: 0.75rem 0; text-align: left; width: 45%;">Item / Description</th>
                    <th style="padding: 0.75rem 0; text-align: center; width: 10%;">Qty</th>
                    <th style="padding: 0.75rem 0; text-align: right; width: 20%;">Unit Price</th>
                    <th style="padding: 0.75rem 0; text-align: right; width: 25%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @if($invoice->quote)
                    @foreach($invoice->quote->items as $item)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 1rem 0;">
                            <div style="font-weight: 700; color: #111;">{{ $item->product->name ?? 'Custom Item' }}</div>
                            <div style="font-size: 0.75rem; color: #666;">{{ $item->description }}</div>
                        </td>
                        <td style="padding: 1rem 0; text-align: center;">{{ $item->qty }}</td>
                        <td style="padding: 1rem 0; text-align: right;">{{ $currentCompany->currency_symbol }}{{ number_format($item->unit_price, 2) }}</td>
                        <td style="padding: 1rem 0; text-align: right; font-weight: 700;">{{ $currentCompany->currency_symbol }}{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 1rem 0;">
                            <div style="font-weight: 700; color: #111;">Rooftop Solar Plant Installation</div>
                            <div style="font-size: 0.75rem; color: #666;">Turnkey project commissioning for solar plant.</div>
                        </td>
                        <td style="padding: 1rem 0; text-align: center;">1</td>
                        <td style="padding: 1rem 0; text-align: right;">{{ $currentCompany->currency_symbol }}{{ number_format($invoice->subtotal, 2) }}</td>
                        <td style="padding: 1rem 0; text-align: right; font-weight: 700;">{{ $currentCompany->currency_symbol }}{{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div style="display: grid; grid-template-columns: 1fr 320px; gap: 2rem; align-items: start;">
            <!-- GST Breakdown -->
            <div style="background: #f9fafb; padding: 1.25rem; border-radius: 0.75rem; font-size: 0.75rem;">
                <h5 style="font-weight: 800; text-transform: uppercase; margin: 0 0 0.75rem 0; color: #333; letter-spacing: 0.02em;">GST Breakdown</h5>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid #ddd; font-weight: 700; color: #555;">
                            <th style="text-align: left; padding-bottom: 0.4rem;">Tax Type</th>
                            <th style="text-align: center; padding-bottom: 0.4rem;">Rate (%)</th>
                            <th style="text-align: right; padding-bottom: 0.4rem;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($invoice->cgst_amount > 0)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 0.4rem 0;">Central GST (CGST)</td>
                            <td style="text-align: center; padding: 0.4rem 0;">{{ number_format($invoice->cgst_rate, 2) }}%</td>
                            <td style="text-align: right; padding: 0.4rem 0; font-weight: 600;">{{ $currentCompany->currency_symbol }}{{ number_format($invoice->cgst_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if($invoice->sgst_amount > 0)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 0.4rem 0;">State GST (SGST)</td>
                            <td style="text-align: center; padding: 0.4rem 0;">{{ number_format($invoice->sgst_rate, 2) }}%</td>
                            <td style="text-align: right; padding: 0.4rem 0; font-weight: 600;">{{ $currentCompany->currency_symbol }}{{ number_format($invoice->sgst_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if($invoice->igst_amount > 0)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 0.4rem 0;">Integrated GST (IGST)</td>
                            <td style="text-align: center; padding: 0.4rem 0;">{{ number_format($invoice->igst_rate, 2) }}%</td>
                            <td style="text-align: right; padding: 0.4rem 0; font-weight: 600;">{{ $currentCompany->currency_symbol }}{{ number_format($invoice->igst_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr style="font-weight: 700; color: #111;">
                            <td style="padding-top: 0.5rem;">Total Tax</td>
                            <td></td>
                            <td style="text-align: right; padding-top: 0.5rem;">{{ $currentCompany->currency_symbol }}{{ number_format($invoice->total_gst, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Totals Column -->
            <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.85rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #666; font-weight: 600;">Subtotal</span>
                    <span style="font-weight: 600;">{{ $currentCompany->currency_symbol }}{{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                @if($invoice->discount > 0)
                <div style="display: flex; justify-content: space-between; color: #ef4444;">
                    <span style="font-weight: 600;">Discount</span>
                    <span style="font-weight: 600;">-{{ $currentCompany->currency_symbol }}{{ number_format($invoice->discount, 2) }}</span>
                </div>
                @endif
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #666; font-weight: 600;">Taxable Value</span>
                    <span style="font-weight: 600;">{{ $currentCompany->currency_symbol }}{{ number_format($invoice->taxable_value, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #666; font-weight: 600;">Total GST</span>
                    <span style="font-weight: 600;">+{{ $currentCompany->currency_symbol }}{{ number_format($invoice->total_gst, 2) }}</span>
                </div>
                <hr style="border: 0; border-top: 1px solid #ddd; margin: 0.25rem 0;">
                <div style="display: flex; justify-content: space-between; font-size: 1.35rem; font-weight: 800; color: #000;">
                    <span>Grand Total</span>
                    <span>{{ $currentCompany->currency_symbol }}{{ number_format($invoice->grand_total, 2) }}</span>
                </div>
            </div>
        </div>

        <div style="margin-top: 5rem; border-top: 1px solid #eee; padding-top: 2rem; display: flex; justify-content: space-between; font-size: 0.75rem; color: #777;">
            <div>
                <p style="font-weight: 700; color: #444; margin: 0 0 0.25rem 0;">Declaration:</p>
                <p style="margin: 0; line-height: 1.4;">We declare that this invoice shows the actual price of the goods and services described and that all particulars are true and correct.</p>
            </div>
            <div style="text-align: right; min-width: 150px; border-top: 1px solid #ddd; margin-top: 1rem; padding-top: 0.5rem; font-weight: 700; color: #444;">
                Authorized Signatory
            </div>
        </div>
    </div>
</x-app-layout>
