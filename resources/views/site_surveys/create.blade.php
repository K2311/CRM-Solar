<x-app-layout title="Record Site Survey">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 0.5rem;">New Site Survey</h1>
                <p style="color: var(--text-muted);">Record site survey details for a potential solar install.</p>
            </div>
            @if($lead)
            <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back to Lead</a>
            @endif
        </div>

        <div class="card glass-card">
            <form action="{{ route('surveys.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                @if($lead)
                    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                    <div style="background: rgba(14, 165, 233, 0.1); border: 1px solid rgba(14, 165, 233, 0.2); border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1.5rem;">
                        <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--primary); margin-bottom: 0.25rem;">Customer Details (Pre-filled from Lead)</h4>
                        <p style="font-size: 0.85rem; color: white;"><b>Name:</b> {{ $lead->customer->name }} | <b>Phone:</b> {{ $lead->customer->phone ?? 'N/A' }} | <b>Email:</b> {{ $lead->customer->email ?? 'N/A' }}</p>
                    </div>
                @else
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label">Select Active Lead</label>
                        <select name="lead_id" class="form-control" required>
                            <option value="">Select a lead...</option>
                            @foreach($leads as $l)
                                <option value="{{ $l->id }}">{{ $l->title }} ({{ $l->customer->name }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label class="form-label">Assigned Technician</label>
                        <select name="technician_id" class="form-control" required>
                            <option value="">Select Technician...</option>
                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}" {{ old('technician_id', auth()->id()) == $tech->id ? 'selected' : '' }}>{{ $tech->name }} ({{ $tech->role }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Survey Date</label>
                        <input type="date" name="survey_date" class="form-control" value="{{ old('survey_date', date('Y-m-d')) }}" required>
                    </div>

                    <div>
                        <label class="form-label">Roof Area (Sq. Ft.)</label>
                        <input type="number" name="roof_area_sqft" class="form-control" value="{{ old('roof_area_sqft') }}" placeholder="e.g. 1200" required>
                    </div>

                    <div>
                        <label class="form-label">Roof Type</label>
                        <select name="roof_type" class="form-control" required>
                            <option value="">Select type...</option>
                            <option value="concrete">RCC Concrete Flat Roof</option>
                            <option value="metal_sheet">Tin/Metal Sheet Sloped Roof</option>
                            <option value="tiles">Tile Sloped Roof</option>
                            <option value="ground">Ground Mount (Open space)</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Shading Obstruction Details</label>
                        <select name="shading_details" class="form-control" required>
                            <option value="">Select shading level...</option>
                            <option value="none">No Shading (Direct sunlight all day)</option>
                            <option value="partial">Partial Shading (Nearby trees/buildings)</option>
                            <option value="high">High Shading (Needs structure height)</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Electricity Utility (DISCOM Name)</label>
                        <input type="text" name="discom_name" class="form-control" value="{{ old('discom_name') }}" placeholder="e.g. Tata Power, BESCOM, MSEDCL" required>
                    </div>

                    <div>
                        <label class="form-label">Sanctioned Load (kW)</label>
                        <input type="number" step="0.01" name="sanctioned_load_kw" class="form-control" value="{{ old('sanctioned_load_kw') }}" placeholder="e.g. 5.0" required>
                    </div>

                    <div>
                        <label class="form-label">Consumer Number (Electricity Account ID)</label>
                        <input type="text" name="consumer_number" class="form-control" value="{{ old('consumer_number') }}" placeholder="e.g. 120098471" required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Roof & Site Photos</label>
                    <input type="file" name="photos[]" class="form-control" multiple style="background: transparent; border: 1px dashed var(--border); padding: 1.5rem; text-align: center; border-radius: 0.75rem;">
                    <small style="color: var(--text-muted); margin-top: 0.5rem; display: block;">You can select multiple photos (max 5MB each).</small>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Site Notes & Recommendations</label>
                    <textarea name="notes" class="form-control" rows="4" placeholder="Mention shading obstacles, cable routing path length, structural suggestions...">{{ old('notes') }}</textarea>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 1.5rem;">
                    @if($lead)
                    <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-outline">Cancel</a>
                    @else
                    <a href="{{ route('dashboard') }}" class="btn btn-outline">Cancel</a>
                    @endif
                    <button type="submit" class="btn btn-primary">Save Survey Report</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
