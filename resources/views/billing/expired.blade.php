<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Expired - Solar CRM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            background: #020617;
            color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 2rem;
        }
    </style>
</head>
<body>
    <div class="card glass-card" style="width: 600px; max-width: 100%; padding: 3rem; text-align: center; border-color: rgba(239,68,68,0.3);">
        <div style="width: 72px; height: 72px; border-radius: 50%; background: rgba(239, 68, 68, 0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 1.5rem auto;">
            <i class="bi bi-shield-slash-fill"></i>
        </div>
        
        <h1 style="font-size: 1.8rem; font-weight: 800; color: white; margin-bottom: 0.5rem;">Demo Trial Period Expired</h1>
        <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 2rem;">
            Your company subscription for <strong>{{ $company->name }}</strong> has expired or been suspended. Upgrade to one of our solar-business plans to restore access to your leads pipeline, quotes generation, and tracker dashboards.
        </p>

        <div style="margin-bottom: 2rem;">
            <img src="{{ asset('pricing_plans.png') }}" alt="Solar CRM Subscription Plans" style="max-width: 100%; border-radius: 1rem; border: 1px solid var(--border); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2.5rem;">
            <div style="display: flex; justify-content: space-between; padding: 1rem 1.5rem; background: rgba(255,255,255,0.02); border-radius: 0.75rem; border: 1px solid var(--border);">
                <span style="font-weight: 700; color: white;">Pro Solar Plan</span>
                <form action="{{ route('billing.upgrade') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="plan" value="pro">
                    <button type="submit" class="btn btn-primary" style="padding: 0.4rem 1.25rem; font-size: 0.8rem; border-radius: 0.5rem;">Upgrade - {{ $company->currency_symbol }}49/mo</button>
                </form>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 1rem 1.5rem; background: rgba(255,255,255,0.02); border-radius: 0.75rem; border: 1px solid var(--border);">
                <span style="font-weight: 700; color: white;">Enterprise Solar Plan</span>
                <form action="{{ route('billing.upgrade') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="plan" value="enterprise">
                    <button type="submit" class="btn btn-primary" style="padding: 0.4rem 1.25rem; font-size: 0.8rem; border-radius: 0.5rem; background: #8b5cf6; border: none;">Upgrade - {{ $company->currency_symbol }}149/mo</button>
                </form>
            </div>
        </div>

        <div style="border-top: 1px solid var(--border); padding-top: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 0.8rem; color: var(--text-muted);">Need help? Support contact: contact@solar.com</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline" style="padding: 0.4rem 1rem; font-size: 0.8rem; border-radius: 0.5rem;"><i class="bi bi-box-arrow-left"></i> Logout</button>
            </form>
        </div>
    </div>
</body>
</html>
