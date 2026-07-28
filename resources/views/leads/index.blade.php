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
        <form action="{{ route('leads.index') }}" method="GET"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            <div>
                <label
                    style="display: block; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem; color: var(--text-muted);">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search client name..."
                    class="form-control">
            </div>
            <div>
                <label
                    style="display: block; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem; color: var(--text-muted);">Stage</label>
                <select name="stage" class="form-control">
                    <option value="">All Stages</option>
                    @foreach(\App\Models\Lead::stages() as $stage)
                        <option value="{{ $stage }}" {{ request('stage') == $stage ? 'selected' : '' }}>
                            {{ strtoupper($stage) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label
                    style="display: block; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem; color: var(--text-muted);">Assigned
                    To</label>
                <select name="assigned" class="form-control">
                    <option value="">All Staff</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('assigned') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">Filter</button>
                <a href="{{ route('leads.index') }}" class="btn btn-outline" title="Clear"><i
                        class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="min-width: 280px;">Customer / Lead</th>
                        <th>Stage</th>
                        <th>Value</th>
                        <th style="min-width: 140px;">Assigned To</th>
                        <th>Created</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                        <tr>
                            {{-- Customer + Lead merged column --}}
                            <td>
                                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(14, 165, 233, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem; flex-shrink: 0; margin-top: 2px;">
                                        {{ substr($lead->customer->name, 0, 1) }}
                                    </div>
                                    <div style="min-width: 0;">
                                        <a href="{{ route('customers.show', $lead->customer) }}"
                                            style="font-weight: 600; color: var(--primary); font-size: 0.8125rem; text-decoration: none;">
                                            {{ $lead->customer->name }}
                                        </a>
                                        <div style="font-weight: 700; font-size: 0.9375rem; margin-top: 2px;">
                                            <a href="{{ route('leads.show', $lead) }}"
                                                style="color: var(--text-main); text-decoration: none;">
                                                {{ $lead->title ?? 'Untitled Project' }}
                                            </a>
                                        </div>
                                        <div style="display: flex; gap: 0.5rem; margin-top: 0.35rem; flex-wrap: wrap;">
                                            @if($lead->source)
                                                <span
                                                    style="font-size: 0.6875rem; padding: 0.15rem 0.5rem; border-radius: 0.375rem; background: rgba(139, 92, 246, 0.1); color: #8b5cf6; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;">
                                                    <i class="bi bi-broadcast" style="font-size: 0.625rem;"></i>
                                                    {{ $lead->source }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Stage --}}
                            <td>
                                <span
                                    style="display: inline-block; padding: 0.35rem 0.75rem; border-radius: 0.5rem; font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: white; background: {{ \App\Models\Lead::stageColors()[$lead->stage] ?? 'var(--border)' }};">
                                    {{ str_replace('_', ' ', $lead->stage) }}
                                </span>
                            </td>

                            {{-- Value --}}
                            <td>
                                <span style="font-weight: 800; font-size: 0.9375rem;">
                                    {{ $currentCompany->currency_symbol }}{{ number_format($lead->value, 2) }}
                                </span>
                            </td>

                            {{-- Assigned To --}}
                            <td>
                                @if($lead->assignedUser)
                                    <div style="display: flex; align-items: center; gap: 0.5rem;"
                                        title="{{ $lead->assignedUser->name }}">
                                        <div
                                            style="width: 26px; height: 26px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), #6366f1); display: flex; align-items: center; justify-content: center; font-size: 0.6875rem; font-weight: 700; color: white; flex-shrink: 0;">
                                            {{ substr($lead->assignedUser->name, 0, 1) }}
                                        </div>
                                        <span
                                            style="font-weight: 500; font-size: 0.8125rem;">{{ $lead->assignedUser->name }}</span>
                                    </div>
                                @else
                                    <span
                                        style="font-size: 0.8125rem; color: var(--text-muted); font-style: italic;">Unassigned</span>
                                @endif
                            </td>

                            {{-- Created --}}
                            <td>
                                <span style="font-size: 0.8125rem; color: var(--text-muted);">
                                    {{ $lead->created_at->format('M d, Y') }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 0.375rem; justify-content: flex-end;">
                                    <a href="{{ route('leads.show', $lead) }}" class="btn btn-outline"
                                        style="width: 30px; height: 30px; padding: 0; font-size: 0.8125rem;"
                                        title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->user()->canDo('leads.edit'))
                                        <a href="{{ route('leads.edit', $lead) }}" class="btn btn-outline"
                                            style="width: 30px; height: 30px; padding: 0; color: var(--primary); border-color: rgba(14, 165, 233, 0.2); font-size: 0.8125rem;"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                    @if(auth()->user()->canDo('leads.delete'))
                                        <form action="{{ route('leads.destroy', $lead) }}" method="POST"
                                            id="del-lead-{{ $lead->id }}" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-outline"
                                                style="width: 30px; height: 30px; padding: 0; color: #ef4444; border-color: rgba(239, 68, 68, 0.2); font-size: 0.8125rem;"
                                                title="Delete"
                                                onclick="swalDelete(this, 'Delete lead \'{{ addslashes($lead->title) }}\'? This cannot be undone.')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 5rem 2rem; color: var(--text-muted);">
                                <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"><i
                                        class="bi bi-funnel"></i></div>
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
