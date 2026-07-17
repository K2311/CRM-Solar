<x-app-layout title="Lead Details">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <a href="{{ route('leads.index') }}" class="btn btn-outline" style="padding: 0.5rem;"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 0.25rem;">{{ $lead->title ?? 'Rooftop Install' }}</h1>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <span class="badge" style="background: {{ \App\Models\Lead::stageColors()[$lead->stage] ?? '#334155' }}; color: white; border: none;">{{ strtoupper($lead->stage) }}</span>
                    <span style="color: var(--text-muted); font-size: 0.875rem;">Assigned to {{ $lead->assignedUser->name ?? 'Nobody' }}</span>
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 1rem;">
            @if(auth()->user()->canDo('quotes.create'))
            <a href="{{ route('quotes.create', ['lead_id' => $lead->id]) }}" class="btn btn-outline" style="border-style: dashed;"><i class="bi bi-file-earmark-plus"></i> Create Quote</a>
            @endif
            @if(auth()->user()->canDo('leads.edit'))
            <a href="{{ route('leads.edit', $lead) }}" class="btn btn-primary"><i class="bi bi-pencil-square"></i> Manage Lead</a>
            @endif
        </div>
    </div>

    <!-- Pipeline Progress -->
    <div class="card" style="margin-bottom: 2rem; padding: 1rem;">
        <div style="display: flex; justify-content: space-between; position: relative;">
            <div style="position: absolute; top: 1.4rem; left: 1rem; right: 1rem; height: 4px; background: var(--border); z-index: 1;"></div>
            @php $met = true; @endphp
            @foreach(\App\Models\Lead::stages() as $stage)
            <div style="z-index: 2; display: flex; flex-direction: column; align-items: center; width: 80px;">
                <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: {{ $met ? ( $lead->stage === $stage ? 'var(--primary)' : '#334155') : 'var(--bg-main)' }}; border: 3px solid {{ $met ? 'var(--primary)' : 'var(--border)' }}; display: flex; align-items: center; justify-content: center; color: white;">
                    @if($met && $lead->stage !== $stage) <i class="bi bi-check-lg" style="color: var(--primary); font-weight: bold;"></i> @else <i class="bi bi-circle"></i> @endif
                </div>
                <div style="margin-top: 0.5rem; font-size: 0.65rem; text-transform: uppercase; font-weight: 800; text-align: center; color: {{ $met ? 'white' : 'var(--text-muted)' }}">{{ str_replace('_', ' ', $stage) }}</div>
            </div>
            @if($lead->stage === $stage) @php $met = false; @endphp @endif
            @endforeach
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Lead Profile info -->
            <div class="card">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <div>
                        <h4 style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1rem; text-transform: uppercase;">Lead Details</h4>
                        <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-muted);">Acquisition Source</span>
                                <span style="font-weight: 600;">{{ $lead->source ?? 'Not Set' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-muted);">Estimated Value</span>
                                <span style="font-weight: 800; color: var(--primary);">${{ number_format($lead->value, 2) }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-muted);">Close Probability</span>
                                <span style="font-weight: 600;">{{ $lead->stage === 'won' ? '100' : ($lead->stage === 'lost' ? '0' : '65') }}%</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1rem; text-transform: uppercase;">Customer</h4>
                        <div style="display: flex; gap: 1rem;">
                            <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--bg-main); display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.25rem;">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div>
                                <a href="{{ route('customers.show', $lead->customer) }}" style="font-weight: 700; color: white; display: block;">{{ $lead->customer->name }}</a>
                                <span style="font-size: 0.875rem; color: var(--text-muted);">{{ $lead->customer->phone }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quotes List -->
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h4 style="font-size: 1rem; font-weight: 700;">Active Quotes</h4>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Quote #</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lead->quotes as $quote)
                        <tr>
                            <td style="font-weight: 600;">#{{ $quote->quote_number }}</td>
                            <td style="font-weight: 700; color: var(--primary);">${{ number_format($quote->total, 2) }}</td>
                            <td><span class="badge">{{ $quote->status }}</span></td>
                            <td style="text-align: right;">
                                <a href="{{ route('quotes.show', $quote) }}" class="btn btn-outline" style="padding: 0.4rem;"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">No quotes generated yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Activity Log -->
            <div class="card">
                <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.5rem;">Recent Activity</h4>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @foreach($lead->activities as $activity)
                    <div style="padding: 1rem; background: rgba(255,255,255,0.02); border-radius: 0.75rem; border-left: 4px solid var(--primary);">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--primary);">{{ $activity->type }}</span>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $activity->created_at->format('M d, H:i') }}</span>
                        </div>
                        <p style="font-size: 0.875rem;">{{ $activity->description }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div>
            @if($lead->notes)
            <div class="card glass-card" style="margin-bottom: 1.5rem;">
                <h4 style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1rem; text-transform: uppercase;">Lead Notes</h4>
                <p style="font-size: 0.93rem; line-height: 1.6;">{{ $lead->notes }}</p>
            </div>
            @endif

            <div class="card">
                <h4 style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1rem; text-transform: uppercase;">Quick Actions</h4>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;" x-data>
                    <button class="btn btn-outline" style="width: 100%; justify-content: flex-start;" @click="$dispatch('open-activity-modal', {type: 'call'})"><i class="bi bi-telephone"></i> Log Call</button>
                    <button class="btn btn-outline" style="width: 100%; justify-content: flex-start;" @click="$dispatch('open-activity-modal', {type: 'email'})"><i class="bi bi-envelope"></i> Send Email</button>
                    <button class="btn btn-outline" style="width: 100%; justify-content: flex-start;" @click="$dispatch('open-activity-modal', {type: 'meeting'})"><i class="bi bi-calendar-check"></i> Set Site Survey</button>
                    
                    @if($lead->stage !== 'won')
                    <form action="{{ route('leads.update-stage', $lead) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="stage" value="won">
                        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: flex-start;"><i class="bi bi-check-circle"></i> Mark as Won</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Log Activity Modal (Reused) -->
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
                <input type="hidden" name="subject_type" value="App\Models\Lead">
                <input type="hidden" name="subject_id" value="{{ $lead->id }}">
                
                <div class="form-group">
                    <label class="form-label">Activity Type</label>
                    <select name="type" class="form-control" x-model="type" required>
                        <option value="note">Note</option>
                        <option value="call">Phone Call</option>
                        <option value="email">Email</option>
                        <option value="meeting">Meeting / Survey</option>
                        <option value="task">Task</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="What happened?" required></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                    <button type="button" @click="open = false" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Activity</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
