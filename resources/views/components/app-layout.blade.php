<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Solar CRM' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('styles')
    @stack('head_scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-circle">S</div>
                <div class="app-name">Solar CRM</div>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
                
                @if(auth()->user()->canDo('customers.view'))
                <a href="{{ route('customers.index') }}" class="nav-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Customers
                </a>
                @endif

                @if(auth()->user()->canDo('leads.view'))
                <a href="{{ route('leads.index') }}" class="nav-item {{ request()->routeIs('leads.*') ? 'active' : '' }}">
                    <i class="bi bi-funnel"></i> Leads
                </a>
                @endif

                @if(auth()->user()->canDo('quotes.view'))
                <a href="{{ route('quotes.index') }}" class="nav-item {{ request()->routeIs('quotes.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i> Quotes
                </a>
                @endif

                @if(auth()->user()->canDo('installations.view'))
                <a href="{{ route('installations.index') }}" class="nav-item {{ request()->routeIs('installations.*') ? 'active' : '' }}">
                    <i class="bi bi-tools"></i> Installations
                </a>
                @endif

                @if(auth()->user()->canDo('tickets.view'))
                <a href="{{ route('tickets.index') }}" class="nav-item {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
                    <i class="bi bi-headset"></i> Service Tickets
                </a>
                @endif

                @if(auth()->user()->canDo('products.view'))
                <a href="{{ route('products.index') }}" class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i> Products
                </a>
                @endif

                @if(auth()->user()->canDo('payments.view'))
                <a href="{{ route('payments.index') }}" class="nav-item {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                    <i class="bi bi-currency-dollar"></i> Payments
                </a>
                @endif

                @if(auth()->user()->canDo('marketing.view'))
                <a href="{{ route('campaigns.index') }}" class="nav-item {{ request()->routeIs('campaigns.*') || request()->routeIs('templates.*') ? 'active' : '' }}">
                    <i class="bi bi-megaphone"></i> Marketing
                </a>
                @endif

                @if(auth()->user()->canDo('marketing.view'))
                <a href="{{ route('social.index') }}" class="nav-item {{ request()->routeIs('social.*') ? 'active' : '' }}">
                    <i class="bi bi-share"></i> Social Media
                </a>
                @endif

                <div style="margin-top: 2rem; padding: 0 1rem; font-size: 0.75rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                    System
                </div>

                @if(auth()->user()->canDo('settings.view'))
                <a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i> Settings
                </a>
                @endif

                @if(auth()->user()->canDo('team.view'))
                <a href="{{ route('team.index') }}" class="nav-item {{ request()->routeIs('team.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i> Team & Roles
                </a>
                @endif

                @if(auth()->user()->is_super_admin)
                <a href="{{ route('admin.companies') }}" class="nav-item {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i> Companies
                </a>
                @endif
            </nav>

            <div style="padding: 1.5rem; border-top: 1px solid var(--border);">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-item" style="width: 100%; border: none; background: transparent; cursor: pointer;">
                        <i class="bi bi-box-arrow-left"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="navbar">
                <div class="navbar-left">
                    <h2 style="font-size: 1.1rem; font-weight: 600;">{{ $title ?? 'Dashboard' }}</h2>
                </div>
                <div class="navbar-right" style="display: flex; align-items: center; gap: 1.5rem;">
                    @if(session('impersonate_company_id'))
                    <form action="{{ route('admin.stop-impersonating') }}" method="POST">
                        @csrf
                        <button class="badge badge-danger" style="border: none; cursor: pointer;">Exit Impersonation</button>
                    </form>
                    @endif

                    <div class="user-badge" style="display: flex; align-items: center; gap: 0.75rem;">
                        <div class="text-right">
                            <div style="font-size: 0.875rem; font-weight: 600;">{{ auth()->user()->name }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ auth()->user()->company->name ?? 'System' }}</div>
                        </div>
                        <img src="{{ auth()->user()->avatar_url }}" alt="AV" style="width: 36px; height: 36px; border-radius: 50%; border: 2px solid var(--primary);">
                    </div>
                </div>
            </header>

            <div class="page-container animate-fade">
                {{ $slot }}
            </div>
        </main>
    </div>
    @stack('scripts')
    <script>
    // ── SweetAlert2 global config ──────────────────────────────────────────
    const CrmSwal = Swal.mixin({
        background: '#1e293b',
        color: '#f1f5f9',
        confirmButtonColor: '#0ea5e9',
        cancelButtonColor: '#475569',
        customClass: { popup: 'swal-crm-popup' }
    });

    // Session flash toasts
    @if(session('success'))
    CrmSwal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: @json(session('success')),
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
    });
    @endif

    @if(session('error'))
    CrmSwal.fire({
        toast: true,
        position: 'top-end',
        icon: 'error',
        title: @json(session('error')),
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
    });
    @endif

    @if($errors->any())
    CrmSwal.fire({
        icon: 'error',
        title: 'Please fix the following errors',
        html: '<ul style="text-align:left;padding-left:1.2rem;">' +
            @foreach($errors->all() as $error)
            '<li>{{ addslashes($error) }}</li>' +
            @endforeach
        '</ul>',
    });
    @endif

    // Global delete-confirm helper
    // Usage: <button onclick="swalDelete(this)"> on a button inside a <form>
    function swalDelete(btn, msg) {
        const form = btn.closest('form');
        CrmSwal.fire({
            title: 'Are you sure?',
            text: msg || 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
        }).then(result => { if (result.isConfirmed) form.submit(); });
    }

    // Generic confirm helper (non-delete)
    function swalConfirm(btn, title, text, confirmText) {
        const form = btn.closest('form');
        CrmSwal.fire({
            title: title || 'Confirm',
            text: text || 'Are you sure?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: confirmText || 'Yes, proceed!',
            cancelButtonText: 'Cancel',
        }).then(result => { if (result.isConfirmed) form.submit(); });
    }
    </script>
</body>
</html>
