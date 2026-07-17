<x-app-layout title="Service Tickets">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800;">Support Desk</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Manage post-install service and maintenance requests</p>
        </div>
        @if(auth()->user()->canDo('tickets.create'))
        <a href="{{ route('tickets.create') }}" class="btn btn-primary"><i class="bi bi-ticket-perforated"></i> New Ticket</a>
        @endif
    </div>

    <div class="card glass-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Ticket Info</th>
                    <th>Customer</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Assigned</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                <tr>
                    <td>
                        <div style="font-weight: 700;">{{ $ticket->title }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">#STK-{{ $ticket->id }} • {{ $ticket->created_at->diffForHumans() }}</div>
                    </td>
                    <td>{{ $ticket->customer->name }}</td>
                    <td>
                        @php
                            $pClass = match($ticket->priority) {
                                'urgent' => 'badge-danger',
                                'high'   => 'badge-warning',
                                'medium' => 'badge-info',
                                default  => 'badge-secondary'
                            };
                        @endphp
                        <span class="badge {{ $pClass }}">{{ strtoupper($ticket->priority) }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $ticket->status === 'resolved' ? 'badge-success' : 'badge-warning' }}">{{ $ticket->status }}</span>
                    </td>
                    <td>{{ $ticket->assignedUser->name ?? 'Unassigned' }}</td>
                    <td style="text-align: right;">
                        <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-outline" style="padding: 0.4rem;"><i class="bi bi-chat-left-text"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top: 1.5rem;">
            {{ $tickets->links() }}
        </div>
    </div>
</x-app-layout>
