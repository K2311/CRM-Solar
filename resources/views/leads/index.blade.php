<x-app-layout title="Leads Management">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 0.5rem;">Leads</h1>
            <p style="color: var(--text-muted);">Manage your sales pipeline and track potential installations.</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('leads.index', ['view' => 'kanban']) }}" class="btn btn-outline">
                <i class="bi bi-kanban"></i> Kanban view
            </a>
            @if(auth()->user()->canDo('leads.create'))
            <a href="{{ route('leads.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> New Lead
            </a>
            @endif
        </div>
    </div>

    <!-- Filters -->
    <div class="card" style="margin-bottom: 2rem; padding: 1.25rem;">
        <form action="{{ route('leads.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem; color: var(--text-muted);">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search client name..." class="form-control">
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem; color: var(--text-muted);">Stage</label>
                <select name="stage" class="form-control">
                    <option value="">All Stages</option>
                    @foreach(\App\Models\Lead::stages() as $stage)
                        <option value="{{ $stage }}" {{ request('stage') == $stage ? 'selected' : '' }}>{{ strtoupper($stage) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem; color: var(--text-muted);">Assigned To</label>
                <select name="assigned" class="form-control">
                    <option value="">All Staff</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('assigned') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Filter</button>
                <a href="{{ route('leads.index') }}" class="btn btn-outline" title="Clear"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="min-width: 200px;">Lead Title</th>
                        <th style="min-width: 180px;">Customer</th>
                        <th>Stage</th>
                        <th>Value</th>
                        <th style="min-width: 150px;">Assigned To</th>
                        <th>Created</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    <tr>
                        <td>
                            <div style="font-weight: 700; font-size: 0.9375rem;">{{ $lead->title ?? 'Untitled Project' }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Source: {{ $lead->source ?? 'Direct' }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--primary);">{{ $lead->customer->name }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">{{ $lead->customer->company->name ?? 'Solar Business' }}</div>
                        </td>
                        <td>
                            <span class="badge" style="background: {{ \App\Models\Lead::stageColors()[$lead->stage] ?? 'var(--border)' }}; color: white; padding: 0.4rem 0.8rem; border-radius: 0.5rem; font-size: 0.7rem;">
                                {{ strtoupper($lead->stage) }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 800; font-size: 1rem; color: white;">{{ $currentCompany->currency_symbol }}{{ number_format($lead->value, 2) }}</div>
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; color: white;">
                                    {{ substr($lead->assignedUser->name ?? '?', 0, 1) }}
                                </div>
                                <span style="font-weight: 500;">{{ $lead->assignedUser->name ?? 'Unassigned' }}</span>
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 0.875rem; color: var(--text-muted);">{{ $lead->created_at->format('M d, Y') }}</div>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <a href="{{ route('leads.show', $lead) }}" class="btn btn-outline" style="width: 32px; height: 32px; padding: 0;" title="View"><i class="bi bi-eye"></i></a>
                                @if(auth()->user()->canDo('leads.edit'))
                                <a href="{{ route('leads.edit', $lead) }}" class="btn btn-outline" style="width: 32px; height: 32px; padding: 0; color: var(--primary); border-color: rgba(14, 165, 233, 0.2);" title="Edit"><i class="bi bi-pencil"></i></a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 5rem 2rem; color: var(--text-muted);">
                            <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"><i class="bi bi-funnel"></i></div>
                            <div style="font-weight: 600; font-size: 1.125rem;">No leads found</div>
                            <p style="margin-top: 0.5rem;">Try adjusting your filters or search terms.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $leads->links() }}
    </div>
</x-app-layout>
