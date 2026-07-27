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
            <div class="card" x-data>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Activity Log</h3>
                    <button class="btn btn-outline" style="padding: 0.3rem 0.75rem; font-size: 0.8rem;" @click="$dispatch('open-activity-modal', {type: 'note'})"><i class="bi bi-plus-lg"></i> Add Note</button>
                </div>
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    @forelse($ticket->activities as $activity)
                    <div style="display: flex; gap: 1rem;">
                        @if($activity->user)
                            <img src="{{ $activity->user->avatar_url }}" style="width: 32px; height: 32px; border-radius: 50%;">
                        @else
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--bg-main); display: flex; align-items: center; justify-content: center;"><i class="bi bi-robot"></i></div>
                        @endif
                        <div>
                            <div style="font-size: 0.875rem;">
                                <strong>{{ $activity->user->name ?? 'System' }}</strong>
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
    </div>

    <!-- Log Activity Modal -->
    <div x-data="{ open: false, type: 'note' }" 
         @open-activity-modal.window="open = true; type = $event.detail.type || 'note'"
         x-show="open" 
         class="modal-backdrop"
         style="display: none;">
        <div class="card glass-card" @click.away="open = false" style="width: 500px; padding: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-weight: 800;">Log Activity</h3>
                <button @click="open = false" class="btn" style="padding: 0.5rem; background: transparent;"><i class="bi bi-x-lg"></i></button>
            </div>
            
            <form action="{{ route('activities.store') }}" method="POST">
                @csrf
                <input type="hidden" name="subject_type" value="App\Models\ServiceTicket">
                <input type="hidden" name="subject_id" value="{{ $ticket->id }}">
                
                <div class="form-group">
                    <label class="form-label">Activity Type</label>
                    <select name="type" class="form-control" x-model="type" required>
                        <option value="note">Internal Note</option>
                        <option value="call">Phone Call</option>
                        <option value="email">Email</option>
                        <option value="task">Maintenance Task</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Update details..." required></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                    <button type="button" @click="open = false" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Activity</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
