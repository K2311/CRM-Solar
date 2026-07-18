<x-app-layout title="System Companies">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800;">Global Company Administration</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Super-admin view of all registered tenants</p>
        </div>
        <button class="btn btn-primary" x-data @click="$dispatch('open-create-company-modal')">
            <i class="bi bi-plus-lg"></i> Register Company
        </button>
    </div>

    <div class="card glass-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Company Name</th>
                    <th>Domains/Emails</th>
                    <th>Subscription Plan</th>
                    <th>Members</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($companies as $company)
                <tr>
                    <td>
                        <div style="font-weight: 700;">{{ $company->name }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">Slug: {{ $company->slug }}</div>
                    </td>
                    <td>
                        <div style="margin-bottom: 0.25rem;">
                            @if($company->email)
                                {{ $company->email }}
                            @else
                                <span style="color: var(--text-muted); font-style: italic; font-size: 0.85rem;">No Company Email</span>
                            @endif
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                            Owner: {{ $company->owner?->email ?? 'N/A' }}
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span class="badge badge-info" style="font-weight: 800; border: none;">{{ strtoupper($company->plan) }}</span>
                                <span class="badge" style="background: {{ $company->isPlanExpired() ? '#ef4444' : '#10b981' }}; color: white; border: none; font-size: 0.65rem; padding: 0.15rem 0.4rem;">
                                    {{ strtoupper($company->plan_status) }}
                                </span>
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">
                                Expires: {{ $company->plan_expires_at ? $company->plan_expires_at->format('M d, Y') : 'Lifetime' }}
                            </span>
                        </div>
                    </td>
                    <td>
                         <span class="badge badge-info">{{ $company->users_count }} Staff</span>
                    </td>
                    <td style="text-align: right;">
                        <div style="display: flex; justify-content: flex-end; gap: 0.5rem; align-items: center;">
                            <button class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;" 
                                    x-data 
                                    @click="$dispatch('open-edit-plan-modal', { id: '{{ $company->id }}', name: '{{ addslashes($company->name) }}', plan: '{{ $company->plan }}', status: '{{ $company->plan_status }}', expires: '{{ $company->plan_expires_at ? $company->plan_expires_at->format('Y-m-d\TH:i') : '' }}' })">
                                <i class="bi bi-pencil-square"></i> Plan
                            </button>
                            <form action="{{ route('admin.impersonate', $company) }}" method="POST" style="display: inline; margin: 0;">
                                @csrf
                                <button type="submit" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">
                                    <i class="bi bi-incognito"></i> Impersonate
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Create Company Modal -->
    <div x-data="{ open: false }" x-on:open-create-company-modal.window="open = true">
        <template x-teleport="body">
            <div x-show="open" x-transition>
                <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 100; backdrop-filter: blur(4px);">
         
        <div class="card" style="width: 500px; max-width: 90%; background: #0f172a; border: 1px solid var(--border); box-shadow: 0 10px 30px rgba(0,0,0,0.5);" @click.away="open = false">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; color: white;">Register New Company</h3>
            
            <form action="{{ route('admin.companies.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="company_name" class="form-control" required placeholder="SolarTech India Ltd">
                </div>
                <div class="form-group">
                    <label class="form-label">Primary Email (Optional)</label>
                    <input type="email" name="email" class="form-control" placeholder="contact@company.com">
                </div>
                <div style="border-top: 1px dashed var(--border); margin: 1.5rem 0; padding-top: 1rem;">
                    <h4 style="font-size: 0.85rem; font-weight: 800; color: var(--primary); text-transform: uppercase; margin-bottom: 1rem;">Create Owner Account</h4>
                </div>
                <div class="form-group">
                    <label class="form-label">Owner Name</label>
                    <input type="text" name="owner_name" class="form-control" required placeholder="John Doe">
                </div>
                <div class="form-group">
                    <label class="form-label">Owner Email</label>
                    <input type="email" name="owner_email" class="form-control" required placeholder="owner@company.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="Minimum 8 characters">
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                    <button type="button" class="btn btn-outline" @click="open = false">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Tenant & Owner</button>
                </div>
            </form>
                </div>
            </div>
        </template>
    </div>

    <!-- Edit Plan Modal -->
    <div x-data="{ open: false, companyId: '', companyName: '', plan: 'demo', status: 'active', expires: '' }" 
         x-on:open-edit-plan-modal.window="open = true; companyId = $event.detail.id; companyName = $event.detail.name; plan = $event.detail.plan; status = $event.detail.status; expires = $event.detail.expires">
        <template x-teleport="body">
            <div x-show="open" x-transition>
                <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); display: flex; align-items: center; justify-content: center; z-index: 100; backdrop-filter: blur(4px);">
         
        <div class="card" style="width: 450px; max-width: 90%; background: #0f172a; border: 1px solid var(--border); box-shadow: 0 10px 30px rgba(0,0,0,0.5);" @click.away="open = false">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; color: white;">Manage Subscription: <span x-text="companyName" style="color: var(--primary);"></span></h3>
            
            <form :action="'{{ url('/admin/companies') }}/' + companyId + '/update-plan'" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Subscription Plan</label>
                    <select name="plan" class="form-control" x-model="plan">
                        <option value="demo">Demo Trial Plan</option>
                        <option value="pro">Pro Solar Plan</option>
                        <option value="enterprise">Enterprise Solar Plan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="plan_status" class="form-control" x-model="status">
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Expiration Date & Time</label>
                    <input type="datetime-local" name="plan_expires_at" class="form-control" x-model="expires">
                </div>
                
                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                    <button type="button" class="btn btn-outline" @click="open = false">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>
