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
    <link rel="stylesheet" href="{{ asset('css/admin-polish.css') }}?v=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin-premium.css') }}?v=4">
    <!-- Custom CSS per page -->
    @stack('styles')
</head>
<body>
@php
    try {
        $ac = \App\Http\Controllers\ServiceRequestController::actionCounts();
    } catch (\Throwable $e) {
        $ac = ['orders' => 0, 'vehicles' => 0, 'payments' => 0, 'requests' => 0, 'tickets' => 0, 'total' => 0];
    }
    $badgeOrders = $ac['orders'];
    $badgeRequests = $ac['requests'];
    $badgeTotal = $ac['total'];
@endphp

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
                <i class="fa-solid fa-chart-pie"></i> Dashboard
            </a>

            <div class="nav-section">Operations</div>
            <a href="{{ route('admin.orders.index') }}" class="nav-item {{ request()->is('admin/orders*') ? 'active' : '' }}">
                <i class="fa-solid fa-cart-shopping"></i> Orders
                <span class="nav-badge" data-badge="orders" {!! $badgeOrders ? '' : 'hidden' !!}>{{ $badgeOrders }}</span>
            </a>
            <a href="{{ route('admin.scrap-vehicles.index') }}" class="nav-item {{ request()->is('admin/scrap-vehicles*') ? 'active' : '' }}">
                <i class="fa-solid fa-truck-monster"></i> Scrap Vehicles
                <span class="nav-badge" data-badge="vehicles" {!! $ac['vehicles'] ? '' : 'hidden' !!}>{{ $ac['vehicles'] }}</span>
            </a>
            <a href="{{ route('admin.payments.index') }}" class="nav-item {{ request()->is('admin/payments*') ? 'active' : '' }}">
                <i class="fa-solid fa-credit-card"></i> Payments
                <span class="nav-badge" data-badge="payments" {!! $ac['payments'] ? '' : 'hidden' !!}>{{ $ac['payments'] }}</span>
            </a>
            <a href="{{ route('admin.tickets.index') }}" class="nav-item {{ request()->is('admin/tickets*') ? 'active' : '' }}">
                <i class="fa-solid fa-headset"></i> Support Tickets
                <span class="nav-badge" data-badge="tickets" {!! $ac['tickets'] ? '' : 'hidden' !!}>{{ $ac['tickets'] }}</span>
            </a>
            <a href="{{ route('admin.service-requests.index') }}" class="nav-item {{ request()->is('admin/service-requests*') ? 'active' : '' }}">
                <i class="fa-solid fa-envelope-open-text"></i> Service Requests
                <span class="nav-badge" data-badge="requests" {!! $badgeRequests ? '' : 'hidden' !!}>{{ $badgeRequests }}</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->is('admin/users*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i> Customers
            </a>

            <div class="nav-section">Catalogue</div>
            <a href="{{ url('/admin/categories') }}" class="nav-item {{ request()->is('admin/categories') ? 'active' : '' }}">
                <i class="fa-solid fa-list"></i> Categories
            </a>
            <a href="{{ url('/admin/subcategories') }}" class="nav-item {{ request()->is('admin/subcategories') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i> Subcategories
            </a>
            <a href="{{ url('/admin/banners') }}" class="nav-item {{ request()->is('admin/banners') ? 'active' : '' }}">
                <i class="fa-solid fa-images"></i> Banners
            </a>

            <div class="nav-section">Content &amp; Settings</div>
            <a href="{{ route('admin.pages.index') }}" class="nav-item {{ request()->is('admin/pages*') ? 'active' : '' }}">
                <i class="fa-regular fa-file-lines"></i> Pages
            </a>
            <a href="{{ url('/admin/faqs') }}" class="nav-item {{ request()->is('admin/faqs') ? 'active' : '' }}">
                <i class="fa-regular fa-circle-question"></i> FAQs
            </a>
            <a href="{{ url('/admin/rewards') }}" class="nav-item {{ request()->is('admin/rewards') ? 'active' : '' }}">
                <i class="fa-solid fa-gift"></i> Rewards
            </a>
            <a href="{{ route('admin.payment-settings.index') }}" class="nav-item {{ request()->is('admin/payment-settings*') ? 'active' : '' }}">
                <i class="fa-solid fa-wallet"></i> Payment Settings
            </a>
            <a href="{{ route('admin.pincodes.index') }}" class="nav-item {{ request()->is('admin/pincodes*') ? 'active' : '' }}">
                <i class="fa-solid fa-location-dot"></i> Service Areas
            </a>
            <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->is('admin/settings*') ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i> Site Settings
            </a>
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ url('/admin/logout') }}" id="adminLogoutForm">
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
            <div class="topbar-left">
                <button type="button" class="mobile-toggle" id="sidebarToggle" aria-label="Open menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <span class="topbar-title">@yield('title', 'Admin Dashboard')</span>
            </div>

            <div class="topbar-profile">
                <!-- Notifications -->
                <div class="dropdown notif-wrap">
                    <button type="button" class="notif-btn" id="notifBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" aria-label="Notifications">
                        <i class="fa-regular fa-bell"></i>
                        <span class="notif-count" id="notifCount" {!! $badgeTotal ? '' : 'hidden' !!}>{{ $badgeTotal > 99 ? '99+' : $badgeTotal }}</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notif-menu" aria-labelledby="notifBtn">
                        <div class="notif-head">
                            <strong>Notifications</strong>
                            <span id="notifSummary" class="notif-summary">{{ $badgeTotal }} item(s) need your action</span>
                        </div>
                        <div class="notif-list" id="notifList">
                            <div class="notif-empty">Loading…</div>
                        </div>
                        <div class="notif-foot">
                            <a href="{{ route('admin.orders.index') }}">Orders</a>
                            <a href="{{ route('admin.tickets.index') }}">Tickets</a>
                            <a href="{{ route('admin.service-requests.index') }}">Service requests</a>
                        </div>
                    </div>
                </div>

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

    @include('partials.logout-confirm')
    <script>
        document.getElementById('adminLogoutForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const form = this;
            confirmLogout(() => form.submit());
        });
    </script>

    <script>
        // Any plain table gets a horizontal-scroll wrapper so nothing overflows on phones/tablets
        document.querySelectorAll('.dashboard-inner table').forEach(function (t) {
            if (!t.closest('.table-responsive')) {
                const w = document.createElement('div');
                w.className = 'table-responsive';
                t.parentNode.insertBefore(w, t);
                w.appendChild(t);
            }
        });

        // Mobile card view: copy each column heading onto its cells (used by the stacked-table CSS)
        document.querySelectorAll('.pr-card table').forEach(function (t) {
            var heads = Array.prototype.map.call(t.querySelectorAll('thead th'), function (h) { return h.textContent.trim(); });
            t.querySelectorAll('tbody tr').forEach(function (tr) {
                Array.prototype.forEach.call(tr.children, function (td, i) { if (heads[i] && !td.hasAttribute('data-label')) td.setAttribute('data-label', heads[i]); });
            });
        });

        // Notification badges: refresh every 30s without reloading the page
        (function () {
            const notifCount = document.getElementById('notifCount');
            const notifList = document.getElementById('notifList');
            const notifSummary = document.getElementById('notifSummary');
            let last = null;

            function setBadge(el, n) {
                if (!el) return;
                el.textContent = n > 99 ? '99+' : n;
                el.hidden = !n;
            }

            async function refresh() {
                try {
                    const res = await fetch("{{ route('admin.notifications') }}", { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
                    if (!res.ok) return;
                    const d = await res.json();
                    setBadge(notifCount, d.total);
                    document.querySelectorAll('[data-badge="orders"]').forEach(e => setBadge(e, d.orders));
                    document.querySelectorAll('[data-badge="requests"]').forEach(e => setBadge(e, d.requests));
                    ['vehicles', 'payments', 'tickets'].forEach(k => document.querySelectorAll('[data-badge="' + k + '"]').forEach(e => setBadge(e, d[k])));
                    document.querySelectorAll('[data-kpi-badge]').forEach(e => setBadge(e, d[e.dataset.kpiBadge]));
                    if (notifSummary) notifSummary.textContent = d.total + ' item(s) need your action';
                    if (notifList) {
                        notifList.innerHTML = d.items.length ? d.items.map(i =>
                            '<a class="notif-item" href="' + escapeHtml(i.url) + '">' +
                            '<span class="notif-ic ' + i.type + '"><i class="fa-solid ' + ({order: 'fa-cart-shopping', vehicle: 'fa-truck-monster', payment: 'fa-credit-card', ticket: 'fa-headset'}[i.type] || 'fa-envelope-open-text') + '"></i></span>' +
                            '<span class="notif-body"><b>' + escapeHtml(i.title) + '</b><small>' + escapeHtml(i.text) + '</small><em>' + escapeHtml(i.time) + '</em></span></a>'
                        ).join('') : '<div class="notif-empty">You are all caught up 🎉</div>';
                    }
                    if (last !== null && d.total > last) { document.title = '(' + d.total + ') ' + document.title.replace(/^\(\d+\)\s*/, ''); }
                    last = d.total;
                } catch (e) { /* ignore network errors */ }
            }
            refresh();
            setInterval(refresh, 30000);
        })();
    </script>

    <!-- Custom JS per page -->
    @stack('scripts')
</body>
</html>
