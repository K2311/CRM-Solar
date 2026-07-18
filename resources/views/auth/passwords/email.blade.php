<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Solar CRM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            background: radial-gradient(circle at top right, #1e1b4b, #0f172a);
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
            <h1 style="font-size: 1.5rem; font-weight: 800; color: white;">Reset Password</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Enter your email to receive a reset link</p>
        </div>

        @if(session('status'))
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 0.5rem; padding: 0.75rem; margin-bottom: 1.5rem; color: #10b981; font-size: 0.875rem; text-align: center;">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 0.5rem; padding: 0.75rem; margin-bottom: 1.5rem; color: #ef4444; font-size: 0.875rem; text-align: center;">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="admin@solar.com" required autofocus>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.8rem;">
                Send Reset Link <i class="bi bi-envelope-fill"></i>
            </button>
        </form>

        <div style="text-align: center; margin-top: 2rem; border-top: 1px solid var(--border); padding-top: 1.5rem;">
            <p style="color: var(--text-muted); font-size: 0.875rem;">Remember your password? <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 600;">Back to login</a></p>
        </div>
    </div>
</body>
</html>
