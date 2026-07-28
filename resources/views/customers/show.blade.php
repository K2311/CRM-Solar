<x-app-layout title="Customer Details">

    {{-- ═══ Header ═══ --}}
    <div class="card glass-card" style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 1.25rem;">
            <div style="width: 64px; height: 64px; border-radius: 1rem; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); display: flex; align-items: center; justify-content: center; font-size: 1.75rem; color: white; font-weight: 800; box-shadow: 0 8px 20px -4px rgba(14, 165, 233, 0.35); flex-shrink: 0;">
                {{ substr($customer->name, 0, 1) }}
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    <h1 style="font-size: 1.5rem; font-weight: 800; margin: 0;">{{ $customer->name }}</h1>
                    @php
                        $statusBadge = match ($customer->status) {
                            'active'   => 'badge-success',
                            'prospect' => 'badge-info',
                            'inactive' => 'badge-danger',
                            default    => 'badge-warning',
                        };
                    @endphp
                    <span class="badge {{ $statusBadge }}">{{ ucfirst($customer->status) }}</span>
                </div>
                <div style="display: flex; gap: 1rem; align-items: center; margin-top: 0.35rem; color: var(--text-muted); font-size: 0.8125rem;">
                    <span><i class="bi bi-hash"></i> CSR-{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <span><i class="bi bi-calendar3"></i> Customer since {{ $customer->created_at->format('M Y') }}</span>
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            @if(auth()->user()->canDo('payments.create'))
                <a href="{{ route('payments.create', ['customer_id' => $customer->id]) }}" class="btn btn-outline">
                    <i class="bi bi-credit-card"></i> Record Payment
                </a>
            @endif
            @if(auth()->user()->canDo('customers.edit'))
                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            @endif
            <button class="btn btn-primary" x-data @click="$dispatch('open-activity-modal')">
                <i class="bi bi-journal-text"></i> Log Activity
            </button>
        </div>
    </div>

    {{-- ═══ Main Grid ═══ --}}
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">

        {{-- ── Left Sidebar ── --}}
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">

            {{-- Contact Information Card --}}
            <div class="card">
                <h4 style="font-size: 0.9375rem; font-weight: 700; margin-bottom: 1.25rem;">
                    <i class="bi bi-person-lines-fill" style="color: var(--primary); margin-right: 0.375rem;"></i>
                    Contact Information
                </h4>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <i class="bi bi-envelope" style="color: var(--text-muted); font-size: 0.875rem; width: 18px; text-align: center;"></i>
                        <div>
                            <div style="font-size: 0.6875rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Email</div>
                            <div style="font-weight: 600; font-size: 0.875rem;">{{ $customer->email ?? 'Not provided' }}</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <i class="bi bi-telephone" style="color: var(--text-muted); font-size: 0.875rem; width: 18px; text-align: center;"></i>
                        <div>
                            <div style="font-size: 0.6875rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Phone</div>
                            <div style="font-weight: 600; font-size: 0.875rem;">{{ $customer->phone ?? 'Not provided' }}</div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                        <i class="bi bi-geo-alt" style="color: var(--text-muted); font-size: 0.875rem; width: 18px; text-align: center; margin-top: 2px;"></i>
                        <div>
                            <div style="font-size: 0.6875rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Address</div>
                            <div style="font-weight: 600; font-size: 0.875rem;">{{ $customer->address ?? 'Not provided' }}</div>
                            @if($customer->city || $customer->state || $customer->zip)
                                <div style="font-size: 0.8125rem; color: var(--text-muted);">
                                    {{ collect([$customer->city, $customer->state, $customer->zip])->filter()->implode(', ') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    @if($customer->source)
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <i class="bi bi-broadcast" style="color: var(--text-muted); font-size: 0.875rem; width: 18px; text-align: center;"></i>
                            <div>
                                <div style="font-size: 0.6875rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Source</div>
                                <div style="font-weight: 600; font-size: 0.875rem;">{{ $customer->source }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Recent Payments Card --}}
            <div class="card">
                <h4 style="font-size: 0.9375rem; font-weight: 700; margin-bottom: 1.25rem;">
                    <i class="bi bi-credit-card-2-front" style="color: var(--primary); margin-right: 0.375rem;"></i>
                    Recent Payments
                </h4>
                @if($customer->payments->isNotEmpty())
                    <div style="overflow-x: auto;">
                        <table class="data-table" style="margin: 0;">
                            <thead>
                                <tr>
                                    <th style="padding: 0.625rem 0.75rem;">Amount</th>
                                    <th style="padding: 0.625rem 0.75rem;">Date</th>
                                    <th style="padding: 0.625rem 0.75rem;">Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->payments as $payment)
                                    <tr>
                                        <td style="padding: 0.625rem 0.75rem; font-weight: 700; font-size: 0.875rem;">
                                            {{ $currentCompany->currency_symbol ?? '₹' }}{{ number_format($payment->amount, 2) }}
                                        </td>
                                        <td style="padding: 0.625rem 0.75rem; font-size: 0.8125rem; color: var(--text-muted);">
                                            {{ $payment->payment_date->format('M d, Y') }}
                                        </td>
                                        <td style="padding: 0.625rem 0.75rem;">
                                            <span class="badge badge-info">{{ $payment->method }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; color: var(--text-muted); font-size: 0.8125rem; padding: 2rem 1rem;">
                        <i class="bi bi-wallet2" style="font-size: 1.5rem; opacity: 0.3; display: block; margin-bottom: 0.5rem;"></i>
                        No payments recorded
                    </div>
                @endif
            </div>
        </div>

        {{-- ── Right Content (Tabs) ── --}}
        <div>
            <div x-data="{ tab: 'timeline' }">
                <div class="tab-container" style="margin-bottom: 1.5rem;">
                    <button class="tab-pill" :class="{ 'active': tab === 'timeline' }" @click="tab = 'timeline'">
                        <i class="bi bi-clock-history"></i> Timeline
                    </button>
                    <button class="tab-pill" :class="{ 'active': tab === 'leads' }" @click="tab = 'leads'">
                        <i class="bi bi-funnel"></i> Leads & Quotes ({{ $customer->leads->count() }})
                    </button>
                    <button class="tab-pill" :class="{ 'active': tab === 'installs' }" @click="tab = 'installs'">
                        <i class="bi bi-tools"></i> Installations ({{ $customer->installations->count() }})
                    </button>
                </div>

                {{-- Timeline Tab --}}
                <div x-show="tab === 'timeline'" x-transition:enter="animate-fade">
                    @if($customer->activities->isNotEmpty())
                        <div style="position: relative; padding-left: 2rem; border-left: 2px solid var(--border); margin-left: 0.5rem; display: flex; flex-direction: column; gap: 1.5rem;">
                            @foreach($customer->activities as $activity)
                                <div style="position: relative;">
                                    <div style="position: absolute; left: -2.35rem; top: 0; width: 12px; height: 12px; border-radius: 50%; background: var(--primary); border: 2px solid var(--bg-main);"></div>
                                    <div class="card glass-card" style="padding: 1rem;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                            <span style="font-weight: 700; text-transform: uppercase; font-size: 0.6875rem; color: var(--primary); letter-spacing: 0.05em;">{{ $activity->type }}</span>
                                            <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $activity->created_at->diffForHumans() }} by {{ $activity->user->name ?? 'System' }}</span>
                                        </div>
                                        <p style="font-size: 0.875rem; margin: 0; line-height: 1.5;">{{ $activity->description }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 4rem 2rem; color: var(--text-muted); background: rgba(255,255,255,0.02); border-radius: 1.5rem; border: 1px dashed var(--border);">
                            <i class="bi bi-clock-history" style="font-size: 2.5rem; margin-bottom: 0.75rem; opacity: 0.3; display: block;"></i>
                            <h4 style="color: var(--text-main); margin-bottom: 0.5rem; font-size: 1rem;">No History Yet</h4>
                            <p style="font-size: 0.8125rem;">Start by logging a call or adding a note for this customer.</p>
                            <button class="btn btn-outline" style="margin-top: 1rem;" @click="$dispatch('open-activity-modal')">
                                <i class="bi bi-plus-lg"></i> Log First Activity
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Leads & Quotes Tab --}}
                <div x-show="tab === 'leads'" x-transition:enter="animate-fade">
                    @forelse($customer->leads as $lead)
                        <div class="card" style="margin-bottom: 0.75rem; padding: 1rem 1.25rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="min-width: 0; flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 0.625rem; flex-wrap: wrap;">
                                        <h4 style="font-size: 0.9375rem; font-weight: 700; margin: 0;">{{ $lead->title ?? 'Untitled Project' }}</h4>
                                        <span class="badge" style="background: {{ \App\Models\Lead::stageColors()[$lead->stage] ?? 'var(--border)' }}; color: white; font-size: 0.625rem; padding: 0.2rem 0.5rem; border-radius: 0.375rem;">
                                            {{ strtoupper(str_replace('_', ' ', $lead->stage)) }}
                                        </span>
                                    </div>
                                    <div style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.35rem;">
                                        Value: <strong style="color: var(--primary);">{{ $currentCompany->currency_symbol }}{{ number_format($lead->value, 2) }}</strong>
                                        @if($lead->assignedUser)
                                            <span style="margin-left: 0.75rem;">
                                                <i class="bi bi-person" style="font-size: 0.75rem;"></i> {{ $lead->assignedUser->name }}
                                            </span>
                                        @endif
                                        @if($lead->expected_close_date)
                                            <span style="margin-left: 0.75rem;">
                                                <i class="bi bi-calendar-event" style="font-size: 0.75rem;"></i> {{ $lead->expected_close_date->format('M d, Y') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('leads.show', $lead) }}" class="btn btn-outline" style="padding: 0.35rem 0.75rem; font-size: 0.75rem; flex-shrink: 0; margin-left: 1rem;">
                                    View <i class="bi bi-arrow-right" style="font-size: 0.625rem;"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 4rem 2rem; color: var(--text-muted); border: 1px dashed var(--border); border-radius: 1.5rem;">
                            <i class="bi bi-funnel" style="font-size: 2.5rem; margin-bottom: 0.75rem; opacity: 0.3; display: block;"></i>
                            <h4 style="color: var(--text-main); margin-bottom: 0.5rem; font-size: 1rem;">No Leads Found</h4>
                            <p style="font-size: 0.8125rem;">This customer doesn't have any sales opportunities yet.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Installations Tab --}}
                <div x-show="tab === 'installs'" x-transition:enter="animate-fade">
                    @forelse($customer->installations as $install)
                        <div class="card" style="margin-bottom: 0.75rem; padding: 1rem 1.25rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="min-width: 0; flex: 1;">
                                    <div style="display: flex; align-items: center; gap: 0.625rem; flex-wrap: wrap;">
                                        <h4 style="font-size: 0.9375rem; font-weight: 700; margin: 0;">System Installation</h4>
                                        @php
                                            $installBadge = match ($install->status) {
                                                'completed' => 'badge-success',
                                                'scheduled' => 'badge-info',
                                                'in_progress' => 'badge-warning',
                                                'cancelled' => 'badge-danger',
                                                default     => 'badge-info',
                                            };
                                        @endphp
                                        <span class="badge {{ $installBadge }}">{{ ucfirst(str_replace('_', ' ', $install->status)) }}</span>
                                    </div>
                                    <div style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.35rem;">
                                        <i class="bi bi-calendar-check" style="font-size: 0.75rem;"></i>
                                        Scheduled: <strong>{{ $install->scheduled_date->format('M d, Y') }}</strong>
                                        @if($install->completed_date)
                                            <span style="margin-left: 0.75rem;">
                                                <i class="bi bi-check-circle" style="font-size: 0.75rem; color: #10b981;"></i>
                                                Completed: <strong>{{ $install->completed_date->format('M d, Y') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('installations.show', $install) }}" class="btn btn-outline" style="padding: 0.35rem 0.75rem; font-size: 0.75rem; flex-shrink: 0; margin-left: 1rem;">
                                    Details <i class="bi bi-arrow-right" style="font-size: 0.625rem;"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 4rem 2rem; color: var(--text-muted); border: 1px dashed var(--border); border-radius: 1.5rem;">
                            <i class="bi bi-tools" style="font-size: 2.5rem; margin-bottom: 0.75rem; opacity: 0.3; display: block;"></i>
                            <h4 style="color: var(--text-main); margin-bottom: 0.5rem; font-size: 1rem;">No Installations</h4>
                            <p style="font-size: 0.8125rem;">No solar systems are currently scheduled or installed for this customer.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ Log Activity Modal ═══ --}}
    <div x-data="{ open: false }"
         @open-activity-modal.window="open = true"
         x-show="open"
         class="modal-backdrop"
         style="display: none;">
        <div class="card glass-card" @click.away="open = false" style="width: 500px; padding: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-weight: 800; margin: 0;">Log Activity</h3>
                <button @click="open = false" class="btn" style="padding: 0.5rem; background: transparent;"><i class="bi bi-x-lg"></i></button>
            </div>

            <form action="{{ route('activities.store') }}" method="POST">
                @csrf
                <input type="hidden" name="subject_type" value="App\Models\Customer">
                <input type="hidden" name="subject_id" value="{{ $customer->id }}">

                <div class="form-group">
                    <label class="form-label">Activity Type</label>
                    <select name="type" class="form-control" required>
                        <option value="note">Note</option>
                        <option value="call">Phone Call</option>
                        <option value="email">Email</option>
                        <option value="meeting">Meeting</option>
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
