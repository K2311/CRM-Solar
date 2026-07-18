<x-app-layout title="Leads Pipeline">
    <div class="page-header">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800;">Sales Pipeline</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Manage and track your lead progression</p>
        </div>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <div style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 0.75rem; display: flex; padding: 0.25rem;">
                <a href="{{ route('leads.index', ['view' => 'kanban']) }}" class="btn {{ request('view') !== 'table' ? 'btn-primary' : 'btn-outline' }}" style="padding: 0.4rem 1rem; border: none; font-size: 0.75rem;">Kanban</a>
                <a href="{{ route('leads.index', ['view' => 'table']) }}" class="btn {{ request('view') === 'table' ? 'btn-primary' : 'btn-outline' }}" style="padding: 0.4rem 1rem; border: none; font-size: 0.75rem;">Table</a>
            </div>
            @if(auth()->user()->canDo('leads.create'))
            <a href="{{ route('leads.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Lead</a>
            @endif
        </div>
    </div>

    @if(request('view') !== 'table')
    <!-- Kanban View -->
    <div class="kanban-container">
        @foreach(\App\Models\Lead::stages() as $stage)
        <div class="kanban-col">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 0 0.5rem;">
                <h4 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em;">
                    {{ str_replace('_', ' ', $stage) }} 
                    <span style="margin-left: 0.5rem; color: var(--primary); font-weight: 800;">{{ ($leadsByStage[$stage] ?? collect())->count() }}</span>
                </h4>
                <div style="width: 8px; height: 8px; border-radius: 50%; background: {{ \App\Models\Lead::stageColors()[$stage] ?? '#eee' }}"></div>
            </div>

            <div style="flex: 1; border-top: 2px solid {{ \App\Models\Lead::stageColors()[$stage] ?? '#334155' }}; padding-top: 1rem; display: flex; flex-direction: column; gap: 1rem;">
                @forelse($leadsByStage[$stage] ?? [] as $lead)
                <div class="card glass-card animate-fade" style="padding: 1.25rem; cursor: pointer; transition: transform 0.2s;" onclick="location.href='{{ route('leads.show', $lead) }}'">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.65rem; font-weight: 800; padding: 0.2rem 0.5rem; border-radius: 4px; background: rgba(255,255,255,0.05); color: var(--text-muted);">{{ $lead->source ?? 'Direct' }}</span>
                        <span style="font-weight: 700; color: var(--primary); font-size: 0.875rem;">{{ $currentCompany->currency_symbol }}{{ number_format($lead->value) }}</span>
                    </div>
                    <h5 style="font-size: 0.93rem; font-weight: 700; margin-bottom: 0.5rem; color: white;">{{ $lead->customer->name }}</h5>
                    <div style="font-size: 0.75rem; color: var(--text-muted); display: flex; flex-direction: column; gap: 0.25rem;">
                        <span><i class="bi bi-person-fill"></i> {{ $lead->assignedUser->name ?? 'Unassigned' }}</span>
                        @if($lead->expected_close_date)
                        <span><i class="bi bi-calendar-event"></i> Close by {{ $lead->expected_close_date->format('M d') }}</span>
                        @endif
                    </div>
                </div>
                @empty
                <div style="text-align: center; color: var(--text-muted); font-size: 0.75rem; padding: 2rem; border: 2px dashed var(--border); border-radius: 1rem;">No leads here</div>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
    @else
    <!-- Table View -->
    <div class="card glass-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Lead Title</th>
                    <th>Value</th>
                    <th>Stage</th>
                    <th>Assigned To</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leads as $lead)
                <tr>
                    <td><a href="{{ route('customers.show', $lead->customer) }}" style="font-weight: 600;">{{ $lead->customer->name }}</a></td>
                    <td>{{ $lead->title ?? 'Rooftop Solar Project' }}</td>
                    <td style="font-weight: 700; color: var(--primary);">{{ $currentCompany->currency_symbol }}{{ number_format($lead->value, 2) }}</td>
                    <td><span class="badge" style="background: {{ \App\Models\Lead::stageColors()[$lead->stage] ?? '#eee' }}; color: white; border: none;">{{ $lead->stage }}</span></td>
                    <td>{{ $lead->assignedUser->name ?? 'Unassigned' }}</td>
                    <td style="text-align: right;">
                        <a href="{{ route('leads.show', $lead) }}" class="btn btn-outline" style="padding: 0.4rem;"><i class="bi bi-eye"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top: 1.5rem;">
            {{ $leads->links() }}
        </div>
    </div>
    @endif
</x-app-layout>
