<x-app-layout title="Installation Project Details">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                <h1 style="font-size: 1.875rem; font-weight: 800;">Installation Project</h1>
                <span class="badge" style="background: {{ $installation->status === 'completed' ? '#10b981' : ($installation->status === 'scheduled' ? '#3b82f6' : '#f59e0b') }}; color: white; border: none;">
                    {{ strtoupper($installation->status) }}
                </span>
            </div>
            <p style="color: var(--text-muted);">{{ $installation->customer->name }} - {{ $installation->system_size_kw }}kW System</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('installations.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back</a>
            @if(auth()->user()->canDo('installations.edit'))
            <a href="{{ route('installations.edit', $installation) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit Details</a>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <!-- 10-Milestone Progress Tracker -->
            <div class="card glass-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 1rem;">
                    <h3 style="font-size: 1.2rem; font-weight: 800;"><i class="bi bi-patch-check-fill" style="color: var(--primary);"></i> 10-Milestone Installation Tracker</h3>
                    @php
                        $completedCount = $installation->milestones->where('status', 'completed')->count();
                    @endphp
                    <span style="font-weight: 700; background: rgba(14, 165, 233, 0.15); color: var(--primary); padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.85rem;">
                        {{ $completedCount }} / 10 Completed
                    </span>
                </div>

                <!-- Progress Bar -->
                <div style="width: 100%; height: 8px; background: rgba(255,255,255,0.05); border-radius: 4px; overflow: hidden; margin-bottom: 2rem;">
                    <div style="width: {{ ($completedCount / 10) * 100 }}%; height: 100%; background: var(--primary); transition: width 0.3s ease;"></div>
                </div>

                <!-- Milestone Accordion/List -->
                <div style="display: flex; flex-direction: column; gap: 1rem;" x-data="{ activeMilestone: null }">
                    @foreach($installation->milestones as $milestone)
                    <div style="border: 1px solid {{ $milestone->status === 'completed' ? 'rgba(16, 185, 129, 0.2)' : 'var(--border)' }}; border-radius: 0.75rem; overflow: hidden; background: {{ $milestone->status === 'completed' ? 'rgba(16, 185, 129, 0.02)' : 'rgba(255,255,255,0.01)' }}">
                        <div style="padding: 1.25rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;" @click="activeMilestone = (activeMilestone === {{ $milestone->id }} ? null : {{ $milestone->id }})">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div style="width: 2rem; height: 2rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.9rem;
                                    background: {{ $milestone->status === 'completed' ? '#10b981' : ($milestone->status === 'in_progress' ? '#f59e0b' : 'rgba(255,255,255,0.05)') }};
                                    color: white;">
                                    @if($milestone->status === 'completed')
                                        <i class="bi bi-check-lg"></i>
                                    @else
                                        {{ $milestone->milestone_number }}
                                    @endif
                                </div>
                                <div>
                                    <h4 style="font-size: 0.95rem; font-weight: 700; color: {{ $milestone->status === 'completed' ? '#10b981' : 'white' }}">{{ $milestone->name }}</h4>
                                    @if($milestone->status === 'completed' && $milestone->completed_at)
                                        <span style="font-size: 0.75rem; color: var(--text-muted);">Completed on {{ $milestone->completed_at->format('M d, Y H:i') }}</span>
                                    @else
                                        <span class="badge" style="background: {{ $milestone->status === 'in_progress' ? '#f59e0b' : '#64748b' }}; font-size: 0.65rem; padding: 0.15rem 0.4rem;">{{ strtoupper($milestone->status) }}</span>
                                    @endif
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted);">
                                @if($milestone->photo_path)
                                    <i class="bi bi-image" style="color: var(--primary);"></i>
                                @endif
                                <i class="bi" :class="activeMilestone === {{ $milestone->id }} ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                            </div>
                        </div>

                        <!-- Accordion Body -->
                        <div x-show="activeMilestone === {{ $milestone->id }}" x-transition style="padding: 0 1.25rem 1.25rem 1.25rem; border-top: 1px solid var(--border); background: rgba(0,0,0,0.15); display: none;">
                            <div style="display: grid; grid-template-columns: {{ $milestone->photo_path ? '1fr 150px' : '1fr' }}; gap: 1.5rem; margin-top: 1rem; margin-bottom: 1.5rem;">
                                <div>
                                    <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 0.25rem;">Milestone Comments / Checklist</span>
                                    <p style="color: white; font-size: 0.85rem; margin: 0; white-space: pre-line;">{{ $milestone->notes ?: 'No description/notes provided.' }}</p>
                                </div>
                                @if($milestone->photo_path)
                                <div>
                                    <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 0.25rem;">Proof Photo</span>
                                    <a href="{{ asset('storage/' . $milestone->photo_path) }}" target="_blank" style="display: block; width: 100%; height: 100px; border-radius: 0.5rem; overflow: hidden; border: 1px solid var(--border);">
                                        <img src="{{ asset('storage/' . $milestone->photo_path) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    </a>
                                </div>
                                @endif
                            </div>

                            <!-- Update Form -->
                            <form action="{{ route('installations.milestone.update', [$installation->id, $milestone->id]) }}" method="POST" enctype="multipart/form-data" style="border-top: 1px dashed var(--border); padding-top: 1rem; margin-top: 1rem;">
                                @csrf
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                    <div class="form-group" style="margin: 0;">
                                        <label class="form-label" style="font-size: 0.75rem;">Status</label>
                                        <select name="status" class="form-control" style="font-size: 0.8rem; height: auto; padding: 0.4rem;">
                                            <option value="pending" {{ $milestone->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="in_progress" {{ $milestone->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="completed" {{ $milestone->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </div>
                                    <div class="form-group" style="margin: 0;">
                                        <label class="form-label" style="font-size: 0.75rem;">Upload Verification Photo</label>
                                        <input type="file" name="photo" class="form-control" style="font-size: 0.8rem; height: auto; padding: 0.2rem;">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" style="font-size: 0.75rem;">Milestone Notes / Checklist comments</label>
                                    <textarea name="notes" class="form-control" rows="2" placeholder="Describe progress, material counts, issues, etc." style="font-size: 0.8rem;">{{ $milestone->notes }}</textarea>
                                </div>
                                <div style="display: flex; justify-content: flex-end;">
                                    <button type="submit" class="btn btn-primary" style="padding: 0.4rem 1.25rem; font-size: 0.8rem;">Update Milestone Status</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Notes Card -->
            <div class="card">
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.5rem;">Installation Notes</h3>
                <div style="color: var(--text-muted); line-height: 1.6; white-space: pre-wrap;">
                    {{ $installation->notes ?: 'No specific notes for this installation.' }}
                </div>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <div class="card">
                <h3 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 1.25rem;">Project Status</h3>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">System Size</div>
                        <div style="font-weight: 700; font-size: 1.25rem;">{{ $installation->system_size_kw }} kW</div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">Scheduled For</div>
                        <div style="font-weight: 600;">{{ $installation->scheduled_date ? $installation->scheduled_date->format('M d, Y') : 'Not scheduled' }}</div>
                    </div>
                    @if($installation->completed_date)
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">Completed On</div>
                        <div style="font-weight: 600; color: #10b981;">{{ $installation->completed_date->format('M d, Y') }}</div>
                    </div>
                    @endif
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">Panel Brand & Qty</div>
                        <div style="font-weight: 600;">{{ $installation->panel_brand ?? 'N/A' }} ({{ $installation->panel_count ?? 0 }} Panels)</div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">Inverter Brand</div>
                        <div style="font-weight: 600;">{{ $installation->inverter_brand ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- GST Invoice Link if generated -->
            @php
                $invoice = \App\Models\GstInvoice::where('installation_id', $installation->id)->first();
            @endphp
            @if($invoice)
            <div class="card" style="border: 1px solid rgba(16, 185, 129, 0.3); background: rgba(16, 185, 129, 0.05);">
                <h3 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; color: #10b981; margin-bottom: 1rem;"><i class="bi bi-file-earmark-ruled-fill"></i> Tax Invoice Generated</h3>
                <p style="font-size: 0.8rem; line-height: 1.4; color: var(--text-muted); margin-bottom: 1.25rem;">A GST-compliant tax invoice was generated automatically when the system was commissioned (Milestone 9).</p>
                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-primary" style="background: #10b981; border: none; justify-content: center; width: 100%;">
                    <i class="bi bi-receipt"></i> View Tax Invoice ({{ $invoice->invoice_number }})
                </a>
            </div>
            @endif

            <div class="card">
                <h3 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 1.25rem;">Project Supervisor</h3>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <img src="{{ $installation->assignedUser->avatar_url ?? 'https://ui-avatars.com/api/?name=?' }}" style="width: 40px; height: 40px; border-radius: 50%;">
                    <div>
                        <div style="font-weight: 600;">{{ $installation->assignedUser->name ?? 'Unassigned' }}</div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">Supervisor / Lead Technician</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
