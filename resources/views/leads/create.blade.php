<x-app-layout title="Create New Lead">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 0.5rem;">New Lead</h1>
                <p style="color: var(--text-muted);">Add a potential project to your pipeline.</p>
            </div>
            <a href="{{ route('leads.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back to list</a>
        </div>

        <div class="card">
            <form action="{{ route('leads.store') }}" method="POST">
                @csrf
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div style="grid-column: span 2;">
                        <label class="form-label">Project Title / Short Description</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="e.g. 5kW Solar Installation - Residential" required>
                    </div>

                    <div>
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-control" required>
                            <option value="">Select a customer...</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id || request('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} ({{ $customer->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Lead Stage</label>
                        <select name="stage" class="form-control" required>
                            @foreach(\App\Models\Lead::stages() as $stage)
                                <option value="{{ $stage }}" {{ old('stage', 'new') == $stage ? 'selected' : '' }}>{{ strtoupper($stage) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Lead Source</label>
                        <select name="source" class="form-control">
                            <option value="">Select source...</option>
                            <option value="referral" {{ old('source') == 'referral' ? 'selected' : '' }}>Referral</option>
                            <option value="google_ads" {{ old('source') == 'google_ads' ? 'selected' : '' }}>Google Ads</option>
                            <option value="dealer" {{ old('source') == 'dealer' ? 'selected' : '' }}>Dealer</option>
                            <option value="walk_in" {{ old('source') == 'walk_in' ? 'selected' : '' }}>Walk-in</option>
                            <option value="housing_society" {{ old('source') == 'housing_society' ? 'selected' : '' }}>Housing Society</option>
                            <option value="other" {{ old('source') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Estimated Value</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);">$</span>
                            <input type="number" name="value" class="form-control" value="{{ old('value') }}" step="0.01" style="padding-left: 2rem;">
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Assigned To</label>
                        <select name="assigned_user_id" class="form-control">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_user_id', auth()->id()) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Expected Close Date</label>
                        <input type="date" name="expected_close_date" class="form-control" value="{{ old('expected_close_date') }}">
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="4" placeholder="Any additional details about this lead...">{{ old('notes') }}</textarea>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--border); pt: 1.5rem;">
                    <a href="{{ route('leads.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Lead</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
