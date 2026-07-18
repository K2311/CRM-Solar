<x-app-layout title="Subscription & Billing">
    <div style="max-width: 100%; margin: 0 auto;">
        
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1 style="font-size: 1.75rem; font-weight: 800; margin-bottom: 0.5rem;">Choose Your Plan</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">
                Upgrade your subscription to unlock premium features and increase your limits.
            </p>
        </div>

        @if($pendingRequest)
            <div style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); color: #f59e0b; padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                <i class="bi bi-clock-history" style="font-size: 1.25rem;"></i>
                <div>
                    <strong>Upgrade Request Pending</strong> — You have requested to upgrade to 
                    <strong style="text-transform: capitalize;">{{ $pendingRequest->requested_plan }}</strong> plan. 
                    The admin is reviewing your payment proof.
                </div>
            </div>
        @endif

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem;" x-data="{ showModal: false, selectedPlan: '', selectedPlanName: '' }">
            @foreach($plans as $key => $plan)
                <div class="card glass-card" style="border: 2px solid {{ $company->plan === $key ? 'var(--primary)' : 'var(--border)' }}; border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; position: relative;">
                    
                    @if($company->plan === $key)
                        <div style="position: absolute; top: -10px; left: 50%; transform: translateX(-50%); background: var(--primary); color: white; padding: 0.2rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">
                            Current Plan
                        </div>
                    @endif

                    <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.25rem; text-transform: capitalize;">{{ $plan['name'] }}</h3>
                    <div style="font-size: 1.75rem; font-weight: 800; margin-bottom: 1rem;">
                        {{ $company->currency_symbol }}{{ $plan['price'] }}<span style="font-size: 0.85rem; color: var(--text-muted); font-weight: normal;">/mo</span>
                    </div>

                    <ul style="list-style: none; padding: 0; margin: 0 0 1rem 0; flex-grow: 1; font-size: 0.85rem;">
                        <li style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            Up to {{ $plan['user_limit'] }} Users
                        </li>
                        <li style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            Up to {{ $plan['lead_limit'] }} Leads
                        </li>
                        
                        @foreach($plan['features'] as $feature => $enabled)
                            <li style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem; color: {{ $enabled ? 'inherit' : 'var(--text-muted)' }};">
                                @if($enabled)
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                @else
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                @endif
                                {{ ucwords(str_replace('_', ' ', $feature)) }}
                            </li>
                        @endforeach
                    </ul>

                    @if($company->plan !== $key)
                        @if($key !== 'demo')
                            @if($pendingRequest)
                                <button disabled style="width: 100%; padding: 0.6rem; background: var(--border); color: var(--text-muted); border: none; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: not-allowed;">
                                    Request Pending...
                                </button>
                            @else
                                <button type="button" 
                                    @click="selectedPlan = '{{ $key }}'; selectedPlanName = '{{ $plan['name'] }}'; showModal = true"
                                    style="width: 100%; padding: 0.6rem; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: opacity 0.2s;">
                                    Request Upgrade
                                </button>
                            @endif
                        @else
                            <button disabled style="width: 100%; padding: 0.6rem; background: var(--border); color: var(--text-muted); border: none; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: not-allowed;">
                                Demo Plan
                            </button>
                        @endif
                    @endif
                </div>
            @endforeach

            <!-- Upgrade Request Modal -->
            <template x-teleport="body">
                <div x-show="showModal" x-transition>
                    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 100; display: flex; align-items: center; justify-content: center;">
                <div class="card glass-card" @click.outside="showModal = false" style="width: 480px; max-width: 95vw; padding: 2rem; border-radius: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(14, 165, 233, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="bi bi-arrow-up-circle-fill"></i>
                        </div>
                        <div>
                            <h3 style="font-size: 1.1rem; font-weight: 800;">Request Plan Upgrade</h3>
                            <p style="color: var(--text-muted); font-size: 0.8rem;">Upload payment proof for admin review.</p>
                        </div>
                    </div>

                    <form action="{{ route('billing.upgrade') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="plan" x-bind:value="selectedPlan">

                        <div style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.2); padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1.25rem; font-size: 0.85rem;">
                            Upgrading to <strong x-text="selectedPlanName" style="text-transform: capitalize;"></strong> plan
                        </div>

                        <div class="form-group">
                            <label class="form-label">Payment Proof <span style="color: #ef4444;">*</span></label>
                            <input type="file" name="payment_proof" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.25rem;">Upload screenshot or PDF of payment receipt (Max: 10MB)</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Transaction ID, bank reference, etc."></textarea>
                        </div>

                        <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">
                                <i class="bi bi-send"></i> Submit Request
                            </button>
                            <button type="button" class="btn btn-outline" @click="showModal = false" style="flex: 1;">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
                    </div>
                </div>
            </template>
        </div>

    </div>
</x-app-layout>
