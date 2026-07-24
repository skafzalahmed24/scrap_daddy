<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Scrap Daddy')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            /* Center on desktop, full width on mobile */
            display: flex;
            justify-content: center;
        }
        
        .mobile-container {
            width: 100%;
            max-width: 480px; /* Mobile app width */
            background-color: #ffffff;
            min-height: 100vh;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            position: relative;
            padding-bottom: 70px; /* Space for bottom nav */
            display: flex;
            flex-direction: column;
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            width: 100%;
            max-width: 480px;
            background: #ffffff;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-around;
            align-items: center;
            height: 65px;
            z-index: 1000;
            padding-bottom: env(safe-area-inset-bottom);
        }

        .nav-item {
            text-decoration: none;
            color: #6c757d;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 0.75rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-item i {
            font-size: 1.25rem;
            margin-bottom: 2px;
        }

        .nav-item.active {
            color: #0d6efd; /* Primary color */
        }
        
        .nav-item.active i {
            font-weight: bold;
        }

        /* Top Header (Optional per view, but good standard) */
        .app-header {
            background: #ffffff;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .app-header-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0;
        }

        .content-area {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="mobile-container">
    @yield('content')

    <!-- Bottom Navigation Bar -->
    <div class="bottom-nav">
        <a href="{{ route('customer.home') }}" class="nav-item {{ request()->routeIs('customer.home') ? 'active' : '' }}">
            <i class="bi bi-house{{ request()->routeIs('customer.home') ? '-fill' : '' }}"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('customer.orders') }}" class="nav-item {{ request()->routeIs('customer.orders') ? 'active' : '' }}">
            <i class="bi bi-card-list"></i>
            <span>My Orders</span>
        </a>
        <a href="{{ route('customer.rewards') }}" class="nav-item {{ request()->routeIs('customer.rewards') ? 'active' : '' }}">
            <i class="bi bi-gift{{ request()->routeIs('customer.rewards') ? '-fill' : '' }}"></i>
            <span>Rewards</span>
        </a>
        <a href="{{ route('customer.profile') }}" class="nav-item {{ request()->routeIs('customer.profile') ? 'active' : '' }}">
            <i class="bi bi-person{{ request()->routeIs('customer.profile') ? '-fill' : '' }}"></i>
            <span>Profile</span>
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Ensure auth token is present
    if (!localStorage.getItem('auth_token')) {
        window.location.href = '/customer/login';
    }
</script>
@stack('scripts')
</body>
</html>
