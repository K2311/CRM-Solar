<x-app-layout title="Customers">
    <div class="card glass-card" style="margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.1rem; font-weight: 700;">Customer Database</h3>
            <div style="display: flex; gap: 1rem;">
                <form action="{{ route('customers.index') }}" method="GET" style="display: flex; gap: 0.5rem;">
                    <input type="text" name="search" class="form-control" placeholder="Search name/email/phone..."
                        value="{{ request('search') }}" style="width: 250px; padding: 0.5rem 1rem;">
                    <button type="submit" class="btn btn-outline" style="padding: 0.5rem 1rem;">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
                @if(auth()->user()->canDo('customers.create'))
                    <a href="{{ route('customers.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add Customer
                    </a>
                @endif
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        @php
                            $location = collect([$customer->city, $customer->state, $customer->zip])
                                ->filter()->implode(', ');
                            $badgeClass = match ($customer->status) {
                                'active' => 'badge-success',
                                'prospect' => 'badge-info',
                                'inactive' => 'badge-danger',
                                default => 'badge-warning',
                            };
                        @endphp
                        <tr>
                            {{-- Customer (Name + ID) with location tooltip --}}
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.625rem;">
                                    <div
                                        style="width: 30px; height: 30px; border-radius: 8px; background: rgba(14, 165, 233, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8125rem; flex-shrink: 0;">
                                        {{ substr($customer->name, 0, 1) }}
                                    </div>
                                    <a href="{{ route('customers.show', $customer) }}"
                                        style="font-weight: 600; color: var(--text-main); text-decoration: none; white-space: nowrap;"
                                        title="{{ $location ?: 'No location' }}">
                                        {{ $customer->name }}
                                        <br>
                                        <span
                                            style="font-weight: 400; color: var(--text-muted); font-size: 0.75rem; font-family: monospace;">(#CSR-{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }})</span>
                                        @if($location)
                                            <i class="bi bi-geo-alt"
                                                style="font-size: 0.6875rem; color: var(--text-muted); margin-left: 0.25rem;"></i>
                                        @endif
                                    </a>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td>
                                <span style="font-size: 0.8125rem;">{{ $customer->email ?? '—' }}</span>
                            </td>

                            {{-- Phone --}}
                            <td>
                                <span style="font-size: 0.8125rem;">{{ $customer->phone ?? '—' }}</span>
                            </td>

                            {{-- Status --}}
                            <td>
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($customer->status) }}</span>
                            </td>

                            {{-- Created --}}
                            <td>
                                <span style="font-size: 0.8125rem; color: var(--text-muted);">
                                    {{ $customer->created_at->format('M d, Y') }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 0.375rem;">
                                    <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline"
                                        style="width: 30px; height: 30px; padding: 0; font-size: 0.8125rem;" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->canDo('customers.edit'))
                                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline"
                                            style="width: 30px; height: 30px; padding: 0; color: var(--primary); border-color: rgba(14, 165, 233, 0.2); font-size: 0.8125rem;"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                    @if(auth()->user()->canDo('customers.delete'))
                                        <button type="button" class="btn btn-outline"
                                            style="width: 30px; height: 30px; padding: 0; color: #ef4444; border-color: rgba(239, 68, 68, 0.2); font-size: 0.8125rem;"
                                            title="Delete" onclick="confirmCustomerDelete({{ $customer->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 4rem 2rem; color: var(--text-muted);">
                                <div style="font-size: 2.5rem; margin-bottom: 0.75rem; opacity: 0.2;">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div style="font-weight: 600; font-size: 1rem;">No customers found</div>
                                <p style="margin-top: 0.5rem; font-size: 0.875rem;">Try adjusting your search or add a new
                                    customer.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 1.5rem;">
            {{ $customers->links() }}
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmCustomerDelete(customerId) {
                fetch(`/customers/${customerId}/check-delete`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.canDelete) {
                            const c = data.counts;
                            CrmSwal.fire({
                                icon: 'error',
                                title: 'Cannot Delete',
                                html: `This customer has <strong>${c.leads}</strong> Leads, <strong>${c.quotes}</strong> Quotes, <strong>${c.installations}</strong> Installations, and <strong>${c.serviceTickets}</strong> Service Tickets.<br><br>Please delete these records first.`,
                                confirmButtonText: 'Understood',
                            });
                        } else {
                            CrmSwal.fire({
                                title: 'Delete this customer?',
                                text: 'This action cannot be undone.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, delete it!',
                                cancelButtonText: 'Cancel',
                            }).then(result => {
                                if (result.isConfirmed) {
                                    const form = document.createElement('form');
                                    form.method = 'POST';
                                    form.action = `/customers/${customerId}`;
                                    form.innerHTML = `@csrf @method('DELETE')`;
                                    document.body.appendChild(form);
                                    form.submit();
                                }
                            });
                        }
                    })
                    .catch(() => {
                        CrmSwal.fire({ icon: 'error', title: 'Error', text: 'Could not verify customer records. Please try again.' });
                    });
            }
        </script>
    @endpush

</x-app-layout>