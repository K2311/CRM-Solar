<x-app-layout title="Customers">
    <div class="card glass-card" style="margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.1rem; font-weight: 700;">Customer Database</h3>
            <div style="display: flex; gap: 1rem;">
                <form action="{{ route('customers.index') }}" method="GET" style="display: flex; gap: 0.5rem;">
                    <input type="text" name="search" class="form-control" placeholder="Search name/email/phone..."
                        value="{{ request('search') }}" style="width: 250px; padding: 0.5rem 1rem;">
                    <button type="submit" class="btn btn-outline" style="padding: 0.5rem 1rem;"><i
                            class="bi bi-search"></i></button>
                </form>
                @if(auth()->user()->canDo('customers.create'))
                    <a href="{{ route('customers.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add Customer
                    </a>
                @endif
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Email & Phone</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div
                                    style="width: 32px; height: 32px; border-radius: 8px; background: rgba(14, 165, 233, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                    {{ substr($customer->name, 0, 1) }}
                                </div>
                                <div>
                                    <a href="{{ route('customers.show', $customer) }}"
                                        style="font-weight: 600; ">{{ $customer->name }}</a>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">ID:
                                        CSR-{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 0.875rem;">{{ $customer->email ?? 'N/A' }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $customer->phone ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div style="font-size: 0.875rem;">{{ $customer->city ?? 'N/A' }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $customer->state ?? '' }}</div>
                        </td>
                        <td>
                            @php
                                $badgeClass = match ($customer->status) {
                                    'active' => 'badge-success',
                                    'prospect' => 'badge-info',
                                    'inactive' => 'badge-danger',
                                    default => 'badge-warning'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $customer->status }}</span>
                        </td>
                        <td style="color: var(--text-muted); font-size: 0.875rem;">
                            {{ $customer->created_at->format('M d, Y') }}
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                                <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline"
                                    style="padding: 0.4rem; border-radius: 0.5rem;"><i class="bi bi-eye"></i></a>
                                @if(auth()->user()->canDo('customers.edit'))
                                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline"
                                        style="padding: 0.4rem; border-radius: 0.5rem;"><i class="bi bi-pencil"></i></a>
                                @endif
                                @if(auth()->user()->canDo('customers.delete'))
                                    <button type="button" class="btn btn-outline"
                                        style="padding: 0.4rem; border-radius: 0.5rem; color: #ef4444; border-color: rgba(239, 68, 68, 0.2);"
                                        onclick="confirmCustomerDelete({{ $customer->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

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