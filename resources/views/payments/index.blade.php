<x-app-layout title="Payment Transactions">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 0.5rem;">Payments</h1>
            <p style="color: var(--text-muted);">Track incoming revenue and client payments.</p>
        </div>
        @if(auth()->user()->canDo('payments.create'))
        <a href="{{ route('payments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Log Payment
        </a>
        @endif
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Reference</th>
                    <th>Method</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @if($payments->count() > 0)
                    @foreach($payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('customers.show', $payment->customer) }}" style="color: var(--primary); text-decoration: none; font-weight: 600;">
                                {{ $payment->customer->name }}
                            </a>
                        </td>
                        <td><span style="font-size: 0.875rem; color: var(--text-muted);">{{ $payment->reference ?? '-' }}</span></td>
                        <td><span class="badge" style="background: rgba(255,255,255,0.1); border: 1px solid var(--border); color: white;">{{ strtoupper($payment->method) }}</span></td>
                        <td style="text-align: right;"><div style="font-weight: 800; color: #10b981;">+{{ number_format($payment->amount, 2) }}</div></td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-muted);">No payments recorded yet.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $payments->links() }}
    </div>
</x-app-layout>
