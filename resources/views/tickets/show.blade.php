<x-app-layout title="Ticket Details">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                <h1 style="font-size: 1.875rem; font-weight: 800;">Ticket #{{ $ticket->id }}</h1>
                <span class="badge" style="background: {{ $ticket->status === 'resolved' ? '#10b981' : ($ticket->status === 'open' ? '#3b82f6' : '#6b7280') }}; color: white;">
                    {{ strtoupper($ticket->status) }}
                </span>
                <span class="badge" style="background: {{ $ticket->priority === 'urgent' ? '#ef4444' : ($ticket->priority === 'high' ? '#f59e0b' : '#3b82f6') }}; color: white;">
                    {{ strtoupper($ticket->priority) }} PRIORITY
                </span>
            </div>
            <p style="color: var(--text-muted);">{{ $ticket->title }}</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('tickets.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back</a>
            @if(auth()->user()->canDo('tickets.edit'))
            <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit Ticket</a>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <!-- Description -->
            <div class="card">
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem;">Description</h3>
                <div style="color: var(--text-muted); line-height: 1.6; white-space: pre-wrap;">
                    {{ $ticket->description ?: 'No description provided.' }}
                </div>
            </div>

            <!-- Activity / Internal Notes -->
            <div class="card">
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.5rem;">Activity Log</h3>
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @forelse($ticket->activities as $activity)
                    <div style="display: flex; gap: 1rem;">
                        <img src="{{ $activity->user->avatar_url }}" style="width: 32px; height: 32px; border-radius: 50%;">
                        <div>
                            <div style="font-size: 0.875rem;">
                                <strong>{{ $activity->user->name }}</strong>
                                <span style="color: var(--text-muted); margin-left: 0.5rem;">{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                            <div style="font-size: 0.875rem; margin-top: 0.25rem;">{{ $activity->description }}</div>
                        </div>
                    </div>
                    @empty
                    <div style="text-align: center; color: var(--text-muted); padding: 2rem;">
                        No activity recorded for this ticket.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <!-- Customer Info -->
            <div class="card">
                <h3 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 1.25rem;">Customer</h3>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div class="logo-circle" style="width: 48px; height: 48px;">{{ substr($ticket->customer->name, 0, 1) }}</div>
                    <div>
                        <div style="font-weight: 700;">{{ $ticket->customer->name }}</div>
                        <a href="mailto:{{ $ticket->customer->email }}" style="font-size: 0.875rem; color: var(--primary); text-decoration: none;">{{ $ticket->customer->email }}</a>
                    </div>
                </div>
                <div style="font-size: 0.875rem; color: var(--text-muted);">
                    <i class="bi bi-geo-alt"></i> {{ $ticket->customer->city }}, {{ $ticket->customer->state }}
                </div>
                <a href="{{ route('customers.show', $ticket->customer) }}" class="btn btn-outline" style="width: 100%; margin-top: 1.5rem; font-size: 0.875rem;">View Profile</a>
            </div>

            <!-- Assignment Info -->
            <div class="card">
                <h3 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 1.25rem;">Assignment</h3>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <img src="{{ $ticket->assignedUser->avatar_url ?? 'https://ui-avatars.com/api/?name=?' }}" style="width: 40px; height: 40px; border-radius: 50%;">
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">Assigned To</div>
                        <div style="font-weight: 600;">{{ $ticket->assignedUser->name ?? 'Unassigned' }}</div>
                    </div>
                </div>
                
                <div style="margin-top: 1.5rem; pt: 1.5rem; border-top: 1px solid var(--border);">
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.5rem;">DATES</div>
                    <div style="font-size: 0.875rem; margin-bottom: 0.5rem;">
                        <strong>Created:</strong> {{ $ticket->created_at->format('M d, Y') }}
                    </div>
                    @if($ticket->resolved_at)
                    <div style="font-size: 0.875rem;">
                        <strong>Resolved:</strong> {{ $ticket->resolved_at->format('M d, Y') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
