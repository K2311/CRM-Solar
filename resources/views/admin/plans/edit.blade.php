<x-app-layout title="Edit Plan">
    <div style="margin-bottom: 2rem;">
        <a href="{{ route('admin.plans.index') }}" style="color: var(--primary); text-decoration: none; font-size: 0.875rem; display: flex; align-items: center; gap: 0.25rem; margin-bottom: 1rem;">
            <i class="bi bi-arrow-left"></i> Back to Plans
        </a>
        <h1 style="font-size: 1.5rem; font-weight: 800;">Edit Subscription Plan: <span style="color: var(--primary);">{{ $plan->name }}</span></h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Modify plan features, limits, and pricing details.</p>
    </div>

    <div class="card glass-card" style="max-width: 600px;">
        <form action="{{ route('admin.plans.update', $plan) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Plan Name</label>
                <input type="text" name="name" class="form-control" required value="{{ $plan->name }}" placeholder="Standard Plan">
            </div>

            <div class="form-group">
                <label class="form-label">Plan Slug (lowercase unique identifier - cannot edit slug to avoid reference errors)</label>
                <input type="text" class="form-control" disabled value="{{ $plan->slug }}" style="background: rgba(255,255,255,0.02); color: var(--text-muted);">
            </div>

            <div class="form-group">
                <label class="form-label">Monthly Price ($)</label>
                <input type="number" step="0.01" name="price" class="form-control" required value="{{ $plan->price }}" placeholder="19.99">
            </div>

            <div class="form-group">
                <label class="form-label">User Limit (number of active team members allowed)</label>
                <input type="number" name="user_limit" class="form-control" required value="{{ $plan->user_limit }}" placeholder="5">
            </div>

            <div class="form-group">
                <label class="form-label">Lead Limit (number of leads allowed in pipeline)</label>
                <input type="number" name="lead_limit" class="form-control" required value="{{ $plan->lead_limit }}" placeholder="100">
            </div>

            <div class="form-group" style="display: flex; gap: 1rem; align-items: center; margin-top: 1.5rem; border-top: 1px solid var(--border); padding-top: 1rem;">
                <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 0;">
                    <input type="checkbox" name="whatsapp_templates" value="1" {{ $plan->whatsapp_templates ? 'checked' : '' }}>
                    Enable WhatsApp Templates Features
                </label>
            </div>

            <div class="form-group" style="display: flex; gap: 1rem; align-items: center; margin-bottom: 1.5rem;">
                <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 0;">
                    <input type="checkbox" name="branding" value="1" {{ $plan->branding ? 'checked' : '' }}>
                    Enable Custom Branding Features
                </label>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">Update Subscription Plan</button>
                <a href="{{ route('admin.plans.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
