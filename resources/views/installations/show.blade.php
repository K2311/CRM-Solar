<x-app-layout title="Installation Project Details">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                <h1 style="font-size: 1.875rem; font-weight: 800;">Installation Project</h1>
                <span class="badge" style="background: {{ $installation->status === 'completed' ? '#10b981' : ($installation->status === 'scheduled' ? '#3b82f6' : '#f59e0b') }}; color: white; border: none;">
                    {{ strtoupper($installation->status) }}
                </span>
            </div>
            <p style="color: var(--text-muted); font-size: 1.05rem;">
                {{ $installation->customer->name }} 
                <span style="opacity: 0.5; margin: 0 0.5rem;">|</span> 
                {{ $installation->system_size_kw ? $installation->system_size_kw . 'kW System' : 'System Size TBD' }}
            </p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('installations.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back</a>
            @if(auth()->user()->canDo('installations.edit'))
                @if($installation->assigned_user_id)
                <form action="{{ route('installations.send-reminder', $installation) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-outline" style="color: #10b981; border-color: #10b981;">
                        <i class="bi bi-whatsapp"></i> Send Reminder
                    </button>
                </form>
                @endif
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
                                <div style="width: 2.25rem; height: 2.25rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.95rem;
                                    background: {{ $milestone->status === 'completed' ? '#10b981' : ($milestone->status === 'in_progress' ? '#f59e0b' : 'var(--bg-light, #f1f5f9)') }};
                                    color: {{ $milestone->status === 'pending' ? 'var(--text-muted)' : 'white' }}; box-shadow: {{ $milestone->status === 'completed' ? '0 0 10px rgba(16,185,129,0.3)' : 'none' }};">
                                    @if($milestone->status === 'completed')
                                        <i class="bi bi-check-lg"></i>
                                    @else
                                        {{ $milestone->milestone_number }}
                                    @endif
                                </div>
                                <div>
                                    <h4 style="font-size: 0.95rem; font-weight: 700; color: {{ $milestone->status === 'completed' ? '#10b981' : 'var(--text)' }}; margin-bottom: 0.15rem;">{{ $milestone->name }}</h4>
                                    @if($milestone->status === 'completed' && $milestone->completed_at)
                                        <span style="font-size: 0.75rem; color: var(--text-muted);"><i class="bi bi-clock-history"></i> Completed on {{ $milestone->completed_at->format('M d, Y H:i') }}</span>
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
                        <div x-show="activeMilestone === {{ $milestone->id }}" x-transition style="padding: 1.5rem; border-top: 1px solid var(--border); background: rgba(255, 255, 255, 0.02); display: none;">
                            <div style="display: grid; grid-template-columns: {{ $milestone->photo_path ? '1fr 150px' : '1fr' }}; gap: 1.5rem; margin-bottom: 1.5rem; background: rgba(0, 0, 0, 0.15); padding: 1.25rem; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.05);">
                                <div>
                                    <span style="font-size: 0.75rem; color: var(--primary); text-transform: uppercase; font-weight: 800; display: block; margin-bottom: 0.5rem;"><i class="bi bi-card-text" style="margin-right: 0.25rem;"></i> Current Notes / Checklist</span>
                                    <p style="color: var(--text); font-size: 0.9rem; margin: 0; white-space: pre-line; line-height: 1.5;">{{ $milestone->notes ?: 'No description/notes provided yet.' }}</p>
                                </div>
                                @if($milestone->photo_path)
                                <div>
                                    <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 0.5rem;">Proof Photo</span>
                                    <a href="{{ asset('storage/' . $milestone->photo_path) }}" target="_blank" style="display: block; width: 100%; height: 100px; border-radius: 0.5rem; overflow: hidden; border: 1px solid var(--border); transition: transform 0.2s; box-shadow: 0 4px 6px rgba(0,0,0,0.2);" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                        <img src="{{ asset('storage/' . $milestone->photo_path) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    </a>
                                </div>
                                @endif
                            </div>

                            <!-- Update Form -->
                            <form action="{{ route('installations.milestone.update', [$installation->id, $milestone->id]) }}" method="POST" enctype="multipart/form-data" style="margin-top: 1rem;">
                                @csrf
                                <h4 style="font-size: 0.85rem; font-weight: 700; margin-bottom: 1rem; color: var(--text);"><i class="bi bi-pencil-square" style="color: var(--primary); margin-right: 0.25rem;"></i> Update Progress</h4>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                                    <div class="form-group" style="margin: 0;">
                                        <label class="form-label" style="font-size: 0.8rem;">Status</label>
                                        <select name="status" class="form-control" style="font-size: 0.9rem; padding: 0.6rem 1rem;">
                                            <option value="pending" {{ $milestone->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="in_progress" {{ $milestone->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="completed" {{ $milestone->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </div>
                                    <div class="form-group" style="margin: 0;">
                                        <label class="form-label" style="font-size: 0.8rem;">Upload Verification Photo</label>
                                        <div style="background: rgba(0,0,0,0.1); border: 1px dashed var(--border); border-radius: 0.5rem; padding: 0.35rem 0.5rem; display: flex; align-items: center; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border)'">
                                            <input type="file" name="photo" style="width: 100%; font-size: 0.85rem; color: var(--text-muted); cursor: pointer;">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-bottom: 1.25rem;">
                                    <label class="form-label" style="font-size: 0.8rem;">Milestone Notes / Checklist comments</label>
                                    <textarea name="notes" class="form-control" rows="2" placeholder="Describe progress, material counts, issues, etc." style="font-size: 0.9rem; padding: 0.75rem 1rem;">{{ $milestone->notes }}</textarea>
                                </div>
                                <div style="display: flex; justify-content: flex-end;">
                                    <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.5rem; font-size: 0.9rem; font-weight: 600; box-shadow: 0 4px 6px rgba(16,185,129,0.2);"><i class="bi bi-save" style="margin-right: 0.5rem;"></i> Update Milestone</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Notes Card -->
            <div class="card glass-card">
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.5rem;"><i class="bi bi-journal-text" style="color: var(--primary); margin-right: 0.5rem;"></i> Installation Notes</h3>
                <div style="color: var(--text-muted); line-height: 1.6; white-space: pre-wrap; font-size: 0.9rem; padding: 1rem; background: rgba(0,0,0,0.02); border-radius: 0.75rem;">
                    {{ $installation->notes ?: 'No specific notes for this installation.' }}
                </div>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <div class="card glass-card">
                <h3 style="font-size: 0.95rem; font-weight: 800; text-transform: uppercase; color: var(--primary); margin-bottom: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 0.75rem;">
                    <i class="bi bi-info-square-fill" style="margin-right: 0.5rem;"></i> Project Details
                </h3>
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <div style="display: flex; align-items: flex-start; gap: 1rem;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(14, 165, 233, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                            <i class="bi bi-lightning-charge-fill"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">System Size</div>
                            <div style="font-weight: 800; font-size: 1.15rem; color: var(--text);">{{ $installation->system_size_kw ? $installation->system_size_kw . ' kW' : 'TBD' }}</div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: flex-start; gap: 1rem;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                            <i class="bi bi-calendar-event-fill"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Scheduled For</div>
                            <div style="font-weight: 600; font-size: 0.95rem;">{{ $installation->scheduled_date ? $installation->scheduled_date->format('M d, Y') : 'Not scheduled' }}</div>
                        </div>
                    </div>
                    
                    @if($installation->completed_date)
                    <div style="display: flex; align-items: flex-start; gap: 1rem;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Completed On</div>
                            <div style="font-weight: 700; color: #10b981; font-size: 0.95rem;">{{ $installation->completed_date->format('M d, Y') }}</div>
                        </div>
                    </div>
                    @endif

                    <div style="display: flex; align-items: flex-start; gap: 1rem;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(139, 92, 246, 0.1); color: #8b5cf6; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Solar Panels</div>
                            <div style="font-weight: 600; font-size: 0.95rem;">{{ $installation->panel_brand ?? 'Not Selected' }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $installation->panel_count ?? 0 }} Panels</div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: flex-start; gap: 1rem;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(236, 72, 153, 0.1); color: #ec4899; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                            <i class="bi bi-plug-fill"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Inverter</div>
                            <div style="font-weight: 600; font-size: 0.95rem;">{{ $installation->inverter_brand ?? 'Not Selected' }}</div>
                        </div>
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
