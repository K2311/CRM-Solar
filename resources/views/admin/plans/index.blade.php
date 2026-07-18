<x-app-layout title="Manage Pricing Plans">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800;">Manage Pricing Plans</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Manage subscription plan tiers, pricing, limits and feature availability.</p>
        </div>
        <a href="{{ route('admin.plans.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Create Plan
        </a>
    </div>

    <div class="card glass-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Plan Name</th>
                    <th>Slug</th>
                    <th>Pricing & Features</th>
                    <th style="text-align: right; width: 180px; min-width: 180px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($plans as $plan)
                <tr>
                    <td>
                        <div style="font-weight: 700;">{{ $plan->name }}</div>
                    </td>
                    <td><span class="badge" style="background: rgba(255,255,255,0.05); color: white; border: none;">{{ $plan->slug }}</span></td>
                    <td>
                        <div style="display: flex; gap: 0.6rem; flex-wrap: wrap; align-items: center;">
                            <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                                <i class="bi bi-currency-dollar"></i>{{ number_format($plan->price, 2) }}
                            </span>
                            <span class="badge" style="background: rgba(255, 255, 255, 0.05); color: #e5e7eb; border: 1px solid rgba(255, 255, 255, 0.1); font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                                <i class="bi bi-people-fill" style="margin-right: 0.25rem;"></i>{{ $plan->user_limit }} Users
                            </span>
                            <span class="badge" style="background: rgba(255, 255, 255, 0.05); color: #e5e7eb; border: 1px solid rgba(255, 255, 255, 0.1); font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                                <i class="bi bi-funnel-fill" style="margin-right: 0.25rem;"></i>{{ number_format($plan->lead_limit) }} Leads
                            </span>
                            @if($plan->whatsapp_templates)
                                <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                                    <i class="bi bi-whatsapp" style="margin-right: 0.25rem;"></i>WhatsApp
                                </span>
                            @endif
                            @if($plan->branding)
                                <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); font-size: 0.75rem; padding: 0.4rem 0.6rem;">
                                    <i class="bi bi-palette-fill" style="margin-right: 0.25rem;"></i>Branding
                                </span>
                            @endif
                        </div>
                    </td>
                    <td style="text-align: right; width: 180px; min-width: 180px; white-space: nowrap;">
                        <div style="display: flex; justify-content: flex-end; gap: 0.5rem; flex-wrap: nowrap; align-items: center;">
                            <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this subscription plan?')" style="display: inline; margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; background: #ef4444; border: none; color: white;">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
