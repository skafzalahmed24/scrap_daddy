<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Scrap Daddy</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon_new.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon_new.ico') }}">
    <!-- Bootstrap 5 CSS for UI Components -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Admin Dashboard CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <!-- Custom CSS per page -->
    @stack('styles')
</head>
<body>

    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('scraplogo.jpeg') }}" alt="Scrap Daddy" class="sidebar-logo">
            <button type="button" class="close-sidebar" id="closeSidebar" aria-label="Close menu"><i class="fa-solid fa-xmark"></i></button>
        </div>
        
        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> DASHBOARD
            </a>
            <a href="{{ url('/admin/categories') }}" class="nav-item {{ request()->is('admin/categories') ? 'active' : '' }}">
                <i class="fa-solid fa-list"></i> Categories
            </a>
            <a href="{{ url('/admin/subcategories') }}" class="nav-item {{ request()->is('admin/subcategories') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i> Subcategories
            </a>
            <a href="{{ url('/admin/banners') }}" class="nav-item {{ request()->is('admin/banners') ? 'active' : '' }}">
                <i class="fa-solid fa-images"></i> Banners
            </a>
            <a href="{{ route('admin.pages.index') }}" class="nav-item {{ request()->is('admin/pages*') ? 'active' : '' }}">
                <i class="fa-regular fa-file-lines"></i> Pages
            </a>
            <a href="{{ url('/admin/faqs') }}" class="nav-item {{ request()->is('admin/faqs') ? 'active' : '' }}">
                <i class="fa-regular fa-circle-question"></i> FAQs
            </a>
            <a href="{{ url('/admin/rewards') }}" class="nav-item {{ request()->is('admin/rewards') ? 'active' : '' }}">
                <i class="fa-solid fa-gift"></i> Rewards Config
            </a>
            <a href="{{ route('admin.orders.index') }}" class="nav-item {{ request()->is('admin/orders') ? 'active' : '' }}">
                <i class="fa-solid fa-cart-shopping"></i> ORDERS
            </a>
            <a href="{{ route('admin.scrap-vehicles.index') }}" class="nav-item {{ request()->is('admin/scrap-vehicles') ? 'active' : '' }}">
                <i class="fa-solid fa-truck-monster"></i> Scrap Vehicles
            </a>
            <a href="{{ route('admin.payments.index') }}" class="nav-item {{ request()->is('admin/payments') ? 'active' : '' }}">
                <i class="fa-solid fa-credit-card"></i> PAYMENTS
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->is('admin/users*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i> USERS
            </a>
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ url('/admin/logout') }}">
                @csrf
                <button type="submit" class="logout-btn w-100 border-0 text-start">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <div style="display: flex; align-items: center;">
                <button type="button" class="mobile-toggle" id="sidebarToggle" aria-label="Open menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
            
            <div class="topbar-profile">
                <div class="profile-info">
                    <div style="text-align: right;">
                        <div class="profile-name">Admin User</div>
                        <div class="profile-role">Super Admin</div>
                    </div>
                    <div class="profile-avatar">A</div>
                </div>
            </div>
        </header>

        <!-- Dashboard Inner / Main Content Area -->
        <div class="dashboard-inner">
            @yield('content')
        </div>
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sidebar Toggle Logic -->
    <script>
        // Escape user-entered text before putting it into innerHTML templates
        window.escapeHtml = (v) => String(v ?? "").replace(/[&<>"']/g, (c) => ({"&": "&amp;", "<": "&lt;", ">": "&gt;", "\"": "&quot;", "'": "&#39;"}[c]));

        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');
        const closeBtn = document.getElementById('closeSidebar');

        function setSidebar(open) {
            sidebar.classList.toggle('show', open);
            overlay.classList.toggle('show', open);
            document.body.style.overflow = open ? 'hidden' : '';
        }

        toggleBtn.addEventListener('click', () => setSidebar(true));
        closeBtn.addEventListener('click', () => setSidebar(false));
        overlay.addEventListener('click', () => setSidebar(false));
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setSidebar(false); });
        sidebar.querySelectorAll('.nav-item').forEach((a) => a.addEventListener('click', () => setSidebar(false)));
        window.addEventListener('resize', () => { if (window.innerWidth > 992) setSidebar(false); });
    </script>

    <!-- Custom JS per page -->
    @stack('scripts')
</body>
</html>
