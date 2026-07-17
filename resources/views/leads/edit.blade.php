<x-app-layout title="Edit Lead">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 0.5rem;">Edit Lead</h1>
                <p style="color: var(--text-muted);">Update details for project: {{ $lead->title }}</p>
            </div>
            <a href="{{ route('leads.show', $lead) }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back to details</a>
        </div>

        <div class="card">
            <form action="{{ route('leads.update', $lead) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div style="grid-column: span 2;">
                        <label class="form-label">Project Title / Short Description</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $lead->title) }}" required>
                    </div>

                    <div>
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-control" required>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id', $lead->customer_id) == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Lead Stage</label>
                        <select name="stage" class="form-control" required>
                            @foreach(\App\Models\Lead::stages() as $stage)
                                <option value="{{ $stage }}" {{ old('stage', $lead->stage) == $stage ? 'selected' : '' }}>{{ strtoupper($stage) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Lead Source</label>
                        <input type="text" name="source" class="form-control" value="{{ old('source', $lead->source) }}">
                    </div>

                    <div>
                        <label class="form-label">Estimated Value</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);">$</span>
                            <input type="number" name="value" class="form-control" value="{{ old('value', $lead->value) }}" step="0.01" style="padding-left: 2rem;">
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Assigned To</label>
                        <select name="assigned_user_id" class="form-control">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_user_id', $lead->assigned_user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Expected Close Date</label>
                        <input type="date" name="expected_close_date" class="form-control" value="{{ old('expected_close_date', $lead->expected_close_date ? $lead->expected_close_date->format('Y-m-d') : '') }}">
                    </div>
                </div>

                <div x-data="{ stage: '{{ $lead->stage }}' }" @change="stage = $event.target.value" style="margin-bottom: 1.5rem;">
                    <div x-show="stage === 'lost'" class="animate-fade" style="margin-top: 1rem;">
                        <label class="form-label" style="color: #ef4444;">Reason for Loss</label>
                        <textarea name="lost_reason" class="form-control" rows="3" placeholder="Why was this lead lost?">{{ old('lost_reason', $lead->lost_reason) }}</textarea>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="4">{{ old('notes', $lead->notes) }}</textarea>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--border); pt: 1.5rem;">
                    <a href="{{ route('leads.show', $lead) }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Lead</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
