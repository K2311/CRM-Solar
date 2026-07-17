<x-app-layout title="Customer Details">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <div style="width: 80px; height: 80px; border-radius: 1.5rem; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: white; font-weight: 800; box-shadow: 0 10px 15px -3px rgba(14, 165, 233, 0.4);">
                {{ substr($customer->name, 0, 1) }}
            </div>
            <div>
                <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 0.25rem;">{{ $customer->name }}</h1>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <span class="badge badge-success">{{ $customer->status }}</span>
                    <span style="color: var(--text-muted); font-size: 0.875rem;"><i class="bi bi-calendar3"></i> Customer since {{ $customer->created_at->format('M Y') }}</span>
                </div>
            </div>
        </div>
        <div style="display: flex; gap: 1rem;">
            @if(auth()->user()->canDo('payments.create'))
            <a href="{{ route('payments.create', ['customer_id' => $customer->id]) }}" class="btn btn-outline"><i class="bi bi-credit-card"></i> Record Payment</a>
            @endif
            @if(auth()->user()->canDo('customers.edit'))
            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline"><i class="bi bi-pencil"></i> Edit Profile</a>
            @endif
            <button class="btn btn-primary" x-data @click="$dispatch('open-activity-modal')"><i class="bi bi-journal-text"></i> Log Activity</button>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
        <!-- Left: Info Cards -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="card">
                <h4 style="font-size: 1rem; margin-bottom: 1.25rem; font-weight: 700;">Contact Information</h4>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Email</div>
                        <div style="font-weight: 600;">{{ $customer->email ?? 'Not provided' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Phone</div>
                        <div style="font-weight: 600;">{{ $customer->phone ?? 'Not provided' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Address</div>
                        <div style="font-weight: 600;">{{ $customer->address }}</div>
                        <div style="font-size: 0.875rem;">{{ $customer->city }}, {{ $customer->state }} {{ $customer->zip }}</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <h4 style="font-size: 1rem; margin-bottom: 1.25rem; font-weight: 700;">Recent Payments</h4>
                @if($customer->payments->isNotEmpty())
                    @foreach($customer->payments as $payment)
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; padding: 0.75rem; background: rgba(255,255,255,0.02); border-radius: 0.75rem;">
                        <div>
                            <div style="font-size: 0.875rem; font-weight: 600;">{{ number_format($payment->amount, 2) }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $payment->payment_date->format('M d, Y') }}</div>
                        </div>
                        <span class="badge badge-info">{{ $payment->method }}</span>
                    </div>
                    @endforeach
                @else
                    <div style="text-align: center; color: var(--text-muted); font-size: 0.875rem; padding: 1rem;">No payments recorded</div>
                @endif
            </div>
        </div>

        <!-- Right: Activity & Related -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Tabs -->
            <div x-data="{ tab: 'timeline' }">
                <div class="tab-container">
                    <button class="tab-pill" :class="{ 'active': tab === 'timeline' }" @click="tab = 'timeline'">Timeline</button>
                    <button class="tab-pill" :class="{ 'active': tab === 'leads' }" @click="tab = 'leads'">Leads & Quotes ({{ $customer->leads->count() }})</button>
                    <button class="tab-pill" :class="{ 'active': tab === 'installs' }" @click="tab = 'installs'">Installations</button>
                </div>

                <!-- Timeline -->
                <div x-show="tab === 'timeline'" class="animate-fade">
                    @if($customer->activities->isNotEmpty())
                        <div style="position: relative; padding-left: 2rem; border-left: 2px solid var(--border); margin-left: 0.5rem; display: flex; flex-direction: column; gap: 2rem;">
                            @foreach($customer->activities as $activity)
                            <div style="position: relative;">
                                <div style="position: absolute; left: -2.35rem; top: 0; width: 12px; height: 12px; border-radius: 50%; background: var(--primary); border: 2px solid var(--bg-main);"></div>
                                <div class="card glass-card" style="padding: 1rem;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                        <span style="font-weight: 700; text-transform: uppercase; font-size: 0.75rem; color: var(--primary);">{{ $activity->type }}</span>
                                        <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $activity->created_at->diffForHumans() }} by {{ $activity->user->name }}</span>
                                    </div>
                                    <p style="font-size: 0.93rem;">{{ $activity->description }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 4rem 2rem; color: var(--text-muted); background: rgba(255,255,255,0.02); border-radius: 1.5rem; border: 1px dashed var(--border);">
                            <i class="bi bi-clock-history" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; display: block;"></i>
                            <h4 style="color: white; margin-bottom: 0.5rem;">No History Yet</h4>
                            <p style="font-size: 0.875rem;">Start by logging a call or adding a note for this customer.</p>
                            <button class="btn btn-outline" style="margin-top: 1.5rem;" @click="$dispatch('open-activity-modal')"><i class="bi bi-plus-lg"></i> Log First Activity</button>
                        </div>
                    @endif
                </div>

                <!-- Leads -->
                <div x-show="tab === 'leads'" class="animate-fade">
                    @forelse($customer->leads as $lead)
                        <div class="card" style="margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <h4 style="font-size: 1rem; margin-bottom: 0.25rem;">{{ $lead->title ?? 'Untitled Project' }}</h4>
                                    <div style="font-size: 0.875rem; color: var(--text-muted);">Stage: <strong>{{ strtoupper($lead->stage) }}</strong> • Value: <strong style="color: var(--primary)">{{ $currentCompany->currency_symbol }}{{ number_format($lead->value, 2) }}</strong></div>
                                </div>
                                <a href="{{ route('leads.show', $lead) }}" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">View Lead</a>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 4rem 2rem; color: var(--text-muted); border: 1px dashed var(--border); border-radius: 1.5rem;">
                            <i class="bi bi-person-badge" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; display: block;"></i>
                            <h4 style="color: white; margin-bottom: 0.5rem;">No Leads Found</h4>
                            <p style="font-size: 0.875rem;">This customer doesn't have any sales opportunities yet.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Installs -->
                <div x-show="tab === 'installs'" class="animate-fade">
                    @forelse($customer->installations as $install)
                        <div class="card" style="margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <h4 style="font-size: 1rem; margin-bottom: 0.25rem;">System Installation</h4>
                                    <div style="font-size: 0.875rem; color: var(--text-muted);">Status: <strong>{{ strtoupper($install->status) }}</strong> • Date: <strong>{{ $install->scheduled_date->format('M d, Y') }}</strong></div>
                                </div>
                                <a href="{{ route('installations.show', $install) }}" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">Details</a>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 4rem 2rem; color: var(--text-muted); border: 1px dashed var(--border); border-radius: 1.5rem;">
                            <i class="bi bi-tools" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; display: block;"></i>
                            <h4 style="color: white; margin-bottom: 0.5rem;">No Installations</h4>
                            <p style="font-size: 0.875rem;">No solar systems are currently scheduled or installed for this customer.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Log Activity Modal -->
    <div x-data="{ open: false }" 
         @open-activity-modal.window="open = true"
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
