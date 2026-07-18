<x-app-layout title="GST Invoices">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800;">GST Tax Invoices</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Manage tax invoices generated upon project commissioning.</p>
        </div>
    </div>

    <div class="card glass-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th style="text-align: right;">Taxable Value</th>
                    <th style="text-align: right;">GST Paid</th>
                    <th style="text-align: right;">Grand Total</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                <tr>
                    <td style="font-weight: 600;">{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->customer->name }}</td>
                    <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                    <td style="text-align: right;">{{ $currentCompany->currency_symbol }}{{ number_format($invoice->taxable_value, 2) }}</td>
                    <td style="text-align: right; color: var(--text-muted);">{{ $currentCompany->currency_symbol }}{{ number_format($invoice->total_gst, 2) }}</td>
                    <td style="text-align: right; font-weight: 700; color: var(--primary);">{{ $currentCompany->currency_symbol }}{{ number_format($invoice->grand_total, 2) }}</td>
                    <td>
                        <span class="badge" style="background: {{ $invoice->status === 'paid' ? '#10b981' : '#f59e0b' }}; color: white; border: none;">{{ strtoupper($invoice->status) }}</span>
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline" style="padding: 0.4rem;"><i class="bi bi-eye"></i> View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 3rem;">
                        No tax invoices generated yet. Complete Milestone 9 on any active project installation.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 1.5rem;">
            {{ $invoices->links() }}
        </div>
    </div>
</x-app-layout>
