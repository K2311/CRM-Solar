<x-app-layout title="Edit Site Survey">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 0.5rem;">Edit Site Survey</h1>
                <p style="color: var(--text-muted);">Update survey parameters for lead: {{ $siteSurvey->lead->title }}</p>
            </div>
            <a href="{{ route('surveys.show', $siteSurvey) }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Cancel</a>
        </div>

        <div class="card glass-card">
            <form action="{{ route('surveys.update', $siteSurvey) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div style="background: rgba(14, 165, 233, 0.1); border: 1px solid rgba(14, 165, 233, 0.2); border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1.5rem;">
                    <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--primary); margin-bottom: 0.25rem;">Customer Details</h4>
                    <p style="font-size: 0.85rem; color: white;"><b>Name:</b> {{ $siteSurvey->customer->name }} | <b>Phone:</b> {{ $siteSurvey->customer->phone ?? 'N/A' }} | <b>Email:</b> {{ $siteSurvey->customer->email ?? 'N/A' }}</p>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label class="form-label">Assigned Technician</label>
                        <select name="technician_id" class="form-control" required>
                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}" {{ old('technician_id', $siteSurvey->technician_id) == $tech->id ? 'selected' : '' }}>{{ $tech->name }} ({{ $tech->role }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Survey Date</label>
                        <input type="date" name="survey_date" class="form-control" value="{{ old('survey_date', $siteSurvey->survey_date ? $siteSurvey->survey_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                    </div>

                    <div>
                        <label class="form-label">Roof Area (Sq. Ft.)</label>
                        <input type="number" name="roof_area_sqft" class="form-control" value="{{ old('roof_area_sqft', $siteSurvey->roof_area_sqft) }}" required>
                    </div>

                    <div>
                        <label class="form-label">Roof Type</label>
                        <select name="roof_type" class="form-control" required>
                            <option value="concrete" {{ old('roof_type', $siteSurvey->roof_type) == 'concrete' ? 'selected' : '' }}>RCC Concrete Flat Roof</option>
                            <option value="metal_sheet" {{ old('roof_type', $siteSurvey->roof_type) == 'metal_sheet' ? 'selected' : '' }}>Tin/Metal Sheet Sloped Roof</option>
                            <option value="tiles" {{ old('roof_type', $siteSurvey->roof_type) == 'tiles' ? 'selected' : '' }}>Tile Sloped Roof</option>
                            <option value="ground" {{ old('roof_type', $siteSurvey->roof_type) == 'ground' ? 'selected' : '' }}>Ground Mount (Open space)</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Shading Obstruction Details</label>
                        <select name="shading_details" class="form-control" required>
                            <option value="none" {{ old('shading_details', $siteSurvey->shading_details) == 'none' ? 'selected' : '' }}>No Shading (Direct sunlight all day)</option>
                            <option value="partial" {{ old('shading_details', $siteSurvey->shading_details) == 'partial' ? 'selected' : '' }}>Partial Shading (Nearby trees/buildings)</option>
                            <option value="high" {{ old('shading_details', $siteSurvey->shading_details) == 'high' ? 'selected' : '' }}>High Shading (Needs structure height)</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Electricity Utility (DISCOM Name)</label>
                        <input type="text" name="discom_name" class="form-control" value="{{ old('discom_name', $siteSurvey->discom_name) }}" required>
                    </div>

                    <div>
                        <label class="form-label">Sanctioned Load (kW)</label>
                        <input type="number" step="0.01" name="sanctioned_load_kw" class="form-control" value="{{ old('sanctioned_load_kw', $siteSurvey->sanctioned_load_kw) }}" required>
                    </div>

                    <div>
                        <label class="form-label">Consumer Number (Electricity Account ID)</label>
                        <input type="text" name="consumer_number" class="form-control" value="{{ old('consumer_number', $siteSurvey->consumer_number) }}" required>
                    </div>
                </div>

                @if($siteSurvey->photos && count($siteSurvey->photos) > 0)
                    <div style="margin-bottom: 1.5rem;">
                        <label class="form-label">Existing Photos</label>
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                            @foreach($siteSurvey->photos as $photo)
                                <div style="position: relative; border-radius: 0.5rem; overflow: hidden; border: 1px solid var(--border); height: 100px;">
                                    <img src="{{ asset('storage/' . $photo) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Add More Roof & Site Photos</label>
                    <input type="file" name="photos[]" class="form-control" multiple style="background: transparent; border: 1px dashed var(--border); padding: 1.5rem; text-align: center; border-radius: 0.75rem;">
                    <small style="color: var(--text-muted); margin-top: 0.5rem; display: block;">You can select multiple photos (max 5MB each).</small>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Site Notes & Recommendations</label>
                    <textarea name="notes" class="form-control" rows="4">{{ old('notes', $siteSurvey->notes) }}</textarea>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 1.5rem;">
                    <a href="{{ route('surveys.show', $siteSurvey) }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Survey Report</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
