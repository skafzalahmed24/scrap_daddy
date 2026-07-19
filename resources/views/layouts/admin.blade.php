<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Scrap Daddy</title>
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
            <img src="/scraplogo.jpeg" alt="Scrap Daddy" class="sidebar-logo">
            <button class="close-sidebar" id="closeSidebar"></button>
        </div>
        
        <nav class="sidebar-nav">
            <a href="/dashboard" class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> DASHBOARD
            </a>
            <a href="/admin/categories" class="nav-item {{ request()->is('admin/categories') ? 'active' : '' }}">
                <i class="fa-solid fa-list"></i> Categories
            </a>
            <a href="/admin/subcategories" class="nav-item {{ request()->is('admin/subcategories') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i> Subcategories
            </a>
            <a href="/admin/banners" class="nav-item {{ request()->is('admin/banners') ? 'active' : '' }}">
                <i class="fa-solid fa-images"></i> Banners
            </a>
            <a href="{{ route('admin.pages.index') }}" class="nav-item {{ request()->is('admin/pages*') ? 'active' : '' }}">
                <i class="fa-regular fa-file-lines"></i> Pages
            </a>
            <a href="#" class="nav-item">
                <i class="fa-solid fa-envelope-open-text"></i> ENQUIRES
            </a>
            <a href="{{ route('admin.orders.index') }}" class="nav-item {{ request()->is('admin/orders') ? 'active' : '' }}">
                <i class="fa-solid fa-cart-shopping"></i> ORDERS
            </a>
            <a href="#" class="nav-item">
                <i class="fa-solid fa-credit-card"></i> PAYMENTS
            </a>
            <a href="#" class="nav-item">
                <i class="fa-solid fa-users"></i> USERS
            </a>
            <a href="#" class="nav-item">
                <i class="fa-solid fa-star"></i> REVIEWS
            </a>
            <a href="#" class="nav-item">
                <i class="fa-solid fa-gear"></i> SETTINGS
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="/" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <div style="display: flex; align-items: center;">
                <button class="mobile-toggle" id="sidebarToggle">
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
        <div class="dashboard-inner p-4">
            @yield('content')
        </div>
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sidebar Toggle Logic -->
    <script>
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');
        const closeBtn = document.getElementById('closeSidebar');

        function toggleSidebar() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        toggleBtn.addEventListener('click', toggleSidebar);
        closeBtn.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);
    </script>

    <!-- Custom JS per page -->
    @stack('scripts')
</body>
</html>
