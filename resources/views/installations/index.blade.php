<x-app-layout title="Installations">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800;">Installation Schedule</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Coordinate and track rooftop solar deployments</p>
        </div>
        @if(auth()->user()->canDo('installations.create'))
        <a href="{{ route('installations.create') }}" class="btn btn-primary"><i class="bi bi-calendar-check"></i> Book Installation</a>
        @endif
    </div>

    <div class="card glass-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>System Size</th>
                    <th>Status</th>
                    <th>Team Lead</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($installations as $install)
                <tr>
                    <td>
                        <div style="font-weight: 700;">{{ $install->customer->name }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $install->customer->phone }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 600;">{{ $install->scheduled_date ? $install->scheduled_date->format('M d, Y') : 'TBD' }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $install->scheduled_date ? $install->scheduled_date->diffForHumans() : '' }}</div>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--primary);">{{ $install->system_size_kw ?? '0' }} kW</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $install->panel_count ?? '0' }} panels</div>
                    </td>
                    <td>
                        @php
                            $stClass = match($install->status) {
                                'completed'   => 'badge-success',
                                'in_progress' => 'badge-warning',
                                'cancelled'   => 'badge-danger',
                                default       => 'badge-info'
                            };
                        @endphp
                        <span class="badge {{ $stClass }}">{{ strtoupper($install->status) }}</span>
                    </td>
                    <td>{{ $install->assignedUser->name ?? 'Unassigned' }}</td>
                    <td style="text-align: right; display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                        <a href="{{ route('installations.show', $install) }}" class="btn btn-outline" style="padding: 0.4rem;" title="View"><i class="bi bi-eye"></i></a>
                        @if(auth()->user()->canDo('installations.update'))
                        <a href="{{ route('installations.edit', $install) }}" class="btn btn-outline" style="padding: 0.4rem;" title="Edit"><i class="bi bi-pencil"></i></a>
                        @endif
                        @if(auth()->user()->canDo('installations.delete'))
                        <form action="{{ route('installations.destroy', $install) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-outline" style="padding: 0.4rem; color: #ef4444; border-color: #fca5a5;" title="Delete" onclick="swalDelete(this, 'Are you sure you want to delete this installation?')"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top: 1.5rem;">
            {{ $installations->links() }}
        </div>
    </div>
</x-app-layout>
