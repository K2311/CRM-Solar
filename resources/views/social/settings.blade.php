<x-app-layout title="Social Media Settings">
    <div class="card glass-card" style="margin-bottom: 2rem;">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1.5rem;">Connect to Meta</h3>
        
        @if($account)
            <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1rem;">
                <i class="bi bi-check-circle-fill"></i> Connected to Facebook (Page ID: {{ $account->page_id }})
            </div>
            @if($account->instagram_account_id)
                <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 1rem; border-radius: 0.75rem; margin-bottom: 1.5rem;">
                    <i class="bi bi-check-circle-fill"></i> Connected to Instagram (Account ID: {{ $account->instagram_account_id }})
                </div>
            @endif
            <a href="{{ route('social.auth.facebook') }}" class="btn btn-primary">Reconnect Account</a>
        @else
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Connect your Facebook account to enable publishing to your Facebook Page and linked Instagram Account.</p>
            <a href="{{ route('social.auth.facebook') }}" class="btn btn-primary">
                <i class="bi bi-facebook"></i> Connect Facebook
            </a>
        @endif
    </div>
</x-app-layout>
