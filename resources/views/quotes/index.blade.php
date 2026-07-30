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
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="min-width: 320px;">Quote / Customer</th>
                        <th>Status</th>
                        <th>Total Value</th>
                        <th>Created</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotes as $quote)
                        <tr>
                            {{-- Quote + Customer merged --}}
                            <td>
                                <div style="font-weight: 700; font-size: 0.9375rem;">
                                    <a href="{{ route('quotes.show', $quote) }}" style="color: var(--text-main); text-decoration: none;">
                                        {{ $quote->quote_number }}
                                    </a>
                                </div>
                                <div style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.2rem;">
                                    <a href="{{ route('customers.show', $quote->customer) }}" style="color: var(--primary); text-decoration: none; font-weight: 600;">
                                        {{ $quote->customer->name }}
                                    </a>
                                    <span style="font-family: monospace; font-size: 0.75rem; color: var(--text-muted); margin-left: 0.25rem;">
                                        (#CSR-{{ str_pad($quote->customer->id, 4, '0', STR_PAD_LEFT) }})
                                    </span>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td>
                                @php
                                    $statusBadge = match ($quote->status) {
                                        'accepted' => 'badge-success',
                                        'sent'     => 'badge-info',
                                        'rejected' => 'badge-danger',
                                        'draft'    => 'badge-warning',
                                        default    => 'badge-warning',
                                    };
                                @endphp
                                <span class="badge {{ $statusBadge }}">{{ strtoupper($quote->status) }}</span>
                            </td>

                            {{-- Total Value --}}
                            <td>
                                <span style="font-weight: 800; font-size: 0.9375rem;">
                                    {{ $currentCompany->currency_symbol }}{{ number_format($quote->total, 2) }}
                                </span>
                            </td>

                            {{-- Created --}}
                            <td>
                                <span style="font-size: 0.8125rem; color: var(--text-muted);">
                                    {{ $quote->created_at->format('M d, Y') }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 0.375rem; justify-content: flex-end;">
                                    <a href="{{ route('quotes.show', $quote) }}" class="btn btn-outline"
                                        style="width: 30px; height: 30px; padding: 0; font-size: 0.8125rem;" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->canDo('quotes.edit'))
                                        <a href="{{ route('quotes.edit', $quote) }}" class="btn btn-outline"
                                            style="width: 30px; height: 30px; padding: 0; color: var(--primary); border-color: rgba(14, 165, 233, 0.2); font-size: 0.8125rem;"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                    @if(auth()->user()->canDo('quotes.delete'))
                                        <button type="button" class="btn btn-outline"
                                            style="width: 30px; height: 30px; padding: 0; color: #ef4444; border-color: rgba(239, 68, 68, 0.2); font-size: 0.8125rem;"
                                            title="Delete"
                                            onclick="confirmQuoteDelete({{ $quote->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 5rem 2rem; color: var(--text-muted);">
                                <div style="font-size: 2.5rem; margin-bottom: 0.75rem; opacity: 0.2;">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <div style="font-weight: 600; font-size: 1rem;">No quotes found</div>
                                <p style="margin-top: 0.5rem; font-size: 0.8125rem;">Create your first quote to get started.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($quotes->hasPages())
        <div style="margin-top: 1.5rem;">
            {{ $quotes->links() }}
        </div>
    @endif

    @push('scripts')
        <script>
            function confirmQuoteDelete(quoteId) {
                fetch(`/quotes/${quoteId}/check-delete`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.canDelete) {
                            const c = data.counts;
                            CrmSwal.fire({
                                icon: 'error',
                                title: 'Cannot Delete',
                                html: `This quote has <strong>${c.installations}</strong> Installations and <strong>${c.payments}</strong> Payments.<br><br>Please delete these records first.`,
                                confirmButtonText: 'Understood',
                            });
                        } else {
                            CrmSwal.fire({
                                title: 'Delete this quote?',
                                text: 'This action cannot be undone.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, delete it!',
                                cancelButtonText: 'Cancel',
                            }).then(result => {
                                if (result.isConfirmed) {
                                    const form = document.createElement('form');
                                    form.method = 'POST';
                                    form.action = `/quotes/${quoteId}`;
                                    form.innerHTML = `@csrf @method('DELETE')`;
                                    document.body.appendChild(form);
                                    form.submit();
                                }
                            });
                        }
                    })
                    .catch(() => {
                        CrmSwal.fire({ icon: 'error', title: 'Error', text: 'Could not verify quote records. Please try again.' });
                    });
            }
        </script>
    @endpush

</x-app-layout>
