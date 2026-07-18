<x-app-layout title="Site Survey Details">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 0.5rem;">Site Survey Report</h1>
                <p style="color: var(--text-muted);">Lead: <a href="{{ route('leads.show', $siteSurvey->lead_id) }}" style="color: var(--primary); text-decoration: underline;">{{ $siteSurvey->lead->title }}</a></p>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a href="{{ route('leads.show', $siteSurvey->lead_id) }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back to Lead</a>
                <a href="{{ route('surveys.edit', $siteSurvey) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit Survey</a>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <!-- Left Panel: Survey Specs -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <div class="card glass-card">
                    <h3 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem;"><i class="bi bi-geo-alt"></i> Site Specifications</h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div>
                            <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); display: block; margin-bottom: 0.25rem;">Roof Area</span>
                            <span style="font-size: 1.1rem; font-weight: 700;">{{ number_format($siteSurvey->roof_area_sqft) }} Sq. Ft.</span>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); display: block; margin-bottom: 0.25rem;">Roof Type</span>
                            <span style="font-size: 1.1rem; font-weight: 700; text-transform: capitalize;">{{ str_replace('_', ' ', $siteSurvey->roof_type) }}</span>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); display: block; margin-bottom: 0.25rem;">Shading Condition</span>
                            <span style="font-size: 1.1rem; font-weight: 700; text-transform: capitalize;">{{ $siteSurvey->shading_details }} Shading</span>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); display: block; margin-bottom: 0.25rem;">DISCOM Utility</span>
                            <span style="font-size: 1.1rem; font-weight: 700;">{{ $siteSurvey->discom_name }}</span>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); display: block; margin-bottom: 0.25rem;">Sanctioned Load</span>
                            <span style="font-size: 1.1rem; font-weight: 700;">{{ $siteSurvey->sanctioned_load_kw }} kW</span>
                        </div>
                        <div>
                            <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); display: block; margin-bottom: 0.25rem;">Consumer Number</span>
                            <span style="font-size: 1.1rem; font-weight: 700; font-family: monospace;">{{ $siteSurvey->consumer_number }}</span>
                        </div>
                    </div>
                </div>

                @if($siteSurvey->notes)
                <div class="card glass-card">
                    <h3 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 1rem;"><i class="bi bi-card-text"></i> Technician's Recommendations & Notes</h3>
                    <p style="white-space: pre-line; line-height: 1.5; color: var(--text-muted);">{{ $siteSurvey->notes }}</p>
                </div>
                @endif

                <div class="card glass-card">
                    <h3 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 1.5rem;"><i class="bi bi-images"></i> Site Photos</h3>
                    @if($siteSurvey->photos && count($siteSurvey->photos) > 0)
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            @foreach($siteSurvey->photos as $photo)
                                <a href="{{ asset('storage/' . $photo) }}" target="_blank" style="display: block; border-radius: 0.75rem; overflow: hidden; border: 1px solid var(--border);">
                                    <img src="{{ asset('storage/' . $photo) }}" style="width: 100%; height: 200px; object-fit: cover; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; color: var(--text-muted); padding: 3rem; border: 2px dashed var(--border); border-radius: 1rem;">
                            <i class="bi bi-image" style="font-size: 2.5rem; display: block; margin-bottom: 1rem;"></i>
                            No photos uploaded for this survey report.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Panel: Metadata info -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div class="card glass-card" style="padding: 1.5rem;">
                    <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem;">Survey Details</h4>
                    <div style="display: flex; flex-direction: column; gap: 1rem; font-size: 0.85rem;">
                        <div>
                            <span style="color: var(--text-muted); display: block;">Survey Date</span>
                            <span style="font-weight: 600;">{{ $siteSurvey->survey_date ? $siteSurvey->survey_date->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        <div>
                            <span style="color: var(--text-muted); display: block;">Technician</span>
                            <span style="font-weight: 600;">{{ $siteSurvey->technician->name ?? 'Unassigned' }}</span>
                        </div>
                        <div>
                            <span style="color: var(--text-muted); display: block;">Customer</span>
                            <span style="font-weight: 600;">{{ $siteSurvey->customer->name }}</span>
                        </div>
                        <hr style="border: 0; border-top: 1px solid var(--border); margin: 0.5rem 0;">
                        <form action="{{ route('surveys.destroy', $siteSurvey) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this survey report?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline" style="width: 100%; border-color: #ef4444; color: #ef4444; justify-content: center;">
                                <i class="bi bi-trash"></i> Delete Report
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
