<x-app-layout title="Quotes & Proposals">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 0.5rem;">Quotes</h1>
            <p style="color: var(--text-muted);">Create and manage project proposals for your clients.</p>
        </div>
        @if(auth()->user()->canDo('quotes.create'))
        <a href="{{ route('quotes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Create Quote
        </a>
        @endif
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <table class="table">
            <thead>
                <tr>
                    <th>Quote #</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Valid Until</th>
                    <th>Total Value</th>
                    <th>Created</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if($quotes->count() > 0)
                    @foreach($quotes as $quote)
                    <tr>
                        <td><span style="font-weight: 700;">{{ $quote->quote_number }}</span></td>
                        <td>
                            <a href="{{ route('customers.show', $quote->customer) }}" style="color: var(--primary); text-decoration: none; font-weight: 600;">
                                {{ $quote->customer->name }}
                            </a>
                        </td>
                        <td>
                            <span class="badge" style="background: {{ $quote->status === 'accepted' ? '#10b981' : ($quote->status === 'draft' ? '#6b7280' : '#3b82f6') }}; color: white;">
                                {{ strtoupper($quote->status) }}
                            </span>
                        </td>
                        <td>{{ $quote->valid_until ? $quote->valid_until->format('M d, Y') : 'N/A' }}</td>
                        <td><div style="font-weight: 700;">{{ $currentCompany->currency_symbol }}{{ number_format($quote->total, 2) }}</div></td>
                        <td style="font-size: 0.875rem; color: var(--text-muted);">{{ $quote->created_at->format('M d, Y') }}</td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <a href="{{ route('quotes.show', $quote) }}" class="btn btn-outline" style="padding: 0.4rem; font-size: 0.75rem;"><i class="bi bi-eye"></i></a>
                                @if(auth()->user()->canDo('quotes.edit'))
                                <a href="{{ route('quotes.edit', $quote) }}" class="btn btn-outline" style="padding: 0.4rem; font-size: 0.75rem;"><i class="bi bi-pencil"></i></a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted);">No quotes found.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</x-app-layout>
