<x-app-layout title="Installation Details">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                <h1 style="font-size: 1.875rem; font-weight: 800;">Installation Project</h1>
                <span class="badge" style="background: {{ $installation->status === 'completed' ? '#10b981' : ($installation->status === 'scheduled' ? '#3b82f6' : '#f59e0b') }}; color: white;">
                    {{ strtoupper($installation->status) }}
                </span>
            </div>
            <p style="color: var(--text-muted);">{{ $installation->customer->name }} - {{ $installation->system_size_kw }}kW System</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('installations.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back</a>
            @if(auth()->user()->canDo('installations.edit'))
            <a href="{{ route('installations.edit', $installation) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit Details</a>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <div class="card">
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.5rem;">Installation Notes</h3>
                <div style="color: var(--text-muted); line-height: 1.6; white-space: pre-wrap;">
                    {{ $installation->notes ?: 'No specific notes for this installation.' }}
                </div>
            </div>

            <!-- More project details can go here -->
        </div>

        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <div class="card">
                <h3 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 1.25rem;">Project Status</h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">System Size</div>
                        <div style="font-weight: 700; font-size: 1.25rem;">{{ $installation->system_size_kw }} kW</div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">Scheduled For</div>
                        <div style="font-weight: 600;">{{ $installation->scheduled_date ? $installation->scheduled_date->format('M d, Y') : 'Not scheduled' }}</div>
                    </div>
                    @if($installation->completed_date)
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">Completed On</div>
                        <div style="font-weight: 600; color: #10b981;">{{ $installation->completed_date->format('M d, Y') }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <h3 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 1.25rem;">Assigned Lead</h3>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <img src="{{ $installation->assignedUser->avatar_url ?? 'https://ui-avatars.com/api/?name=?' }}" style="width: 40px; height: 40px; border-radius: 50%;">
                    <div>
                        <div style="font-weight: 600;">{{ $installation->assignedUser->name ?? 'Unassigned' }}</div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">Project Supervisor</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
