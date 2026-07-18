<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Solar CRM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            background: radial-gradient(circle at top right, #e2e8f0, #f8fafc);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
    </style>
</head>
<body>
    <div class="card glass-card animate-fade" style="width: 100%; max-width: 400px; padding: 2.5rem;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div class="logo-circle" style="margin: 0 auto 1rem; width: 60px; height: 60px; font-size: 1.5rem;">S</div>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main);">Welcome Back</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Login to your Solar CRM account</p>
        </div>

        @if($errors->any())
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 0.5rem; padding: 0.75rem; margin-bottom: 1.5rem; color: #ef4444; font-size: 0.875rem;">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="admin@solar.com" required autofocus>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <label style="font-size: 0.875rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="{{ route('password.request') }}" style="font-size: 0.875rem; color: var(--primary);">Forgot password?</a>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.8rem;">
                Sign In <i class="bi bi-arrow-right"></i>
            </button>
        </form>

        <div style="text-align: center; margin-top: 2rem; border-top: 1px solid var(--border); padding-top: 1.5rem;">
            <p style="color: var(--text-muted); font-size: 0.875rem;">Don't have an account? <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 600;">Register your company</a></p>
        </div>
    </div>
</body>
</html>
