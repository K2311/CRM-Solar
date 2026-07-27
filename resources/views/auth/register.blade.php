<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Solar CRM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            background: radial-gradient(circle at bottom left, #e2e8f0, #f8fafc);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
        }
    </style>
</head>
<body>
    <div class="card glass-card animate-fade" style="width: 100%; max-width: 500px; padding: 2.5rem;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--text-main);">Register Your Company</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Set up your team in minutes</p>
        </div>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            
            @if ($errors->any())
                <div style="background-color: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; font-size: 0.875rem;">
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" placeholder="SolarTech Ltd" required value="{{ old('company_name') }}">
                    @error('company_name')
                        <span class="invalid-feedback" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Owner Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="John Doe" required value="{{ old('name') }}">
                    @error('name')
                        <span class="invalid-feedback" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="john@company.com" required value="{{ old('email') }}">
                    @error('email')
                        <span class="invalid-feedback" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <span class="invalid-feedback" style="color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.8rem; margin-top: 1rem;">
                Create Account <i class="bi bi-rocket-takeoff"></i>
            </button>
        </form>

        <div style="text-align: center; margin-top: 2rem; border-top: 1px solid var(--border); padding-top: 1.5rem;">
            <p style="color: var(--text-muted); font-size: 0.875rem;">Already have an account? <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 600;">Sign In</a></p>
        </div>
    </div>
</body>
</html>
