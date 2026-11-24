<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Scrap Daddy - Turn Scrap into Cash')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('meta')

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon_new.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon_new.ico') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Common Header & Footer CSS -->
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">

    @include('partials.pwa-head')
    @stack('styles')
</head>

<body>
    <!-- Page loader (same idea as the app's loader): shows until the page is ready and while moving between pages -->
    <div id="sdPageLoader" class="sd-loader" aria-hidden="true"><div class="sd-loader-box"><img src="{{ asset('green_truck.png') }}" alt="" class="sd-loader-truck"><div class="sd-loader-bar"><span></span></div></div></div>

    @include('layouts.header')

    <main>
        @yield('content')
    </main>

    @include('layouts.footer')

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Location Detection Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const locText = document.getElementById('locationText');
            const locTextMobile = document.getElementById('locationTextMobile');

            function updateLocationText(text) {
                if (locText) locText.innerText = text;
                if (locTextMobile) locTextMobile.innerText = text;
                
                const locTextMobile2 = document.getElementById('locationTextMobile2');
                if (locTextMobile2) {
                    locTextMobile2.innerText = text;
                    locTextMobile2.nextElementSibling.innerText = 'Detected automatically';
                }
            }

            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;

                    // Reverse geocoding using free Nominatim API
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&zoom=10`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.address) {
                                // Fallbacks for different location granularity
                                const locationName = data.address.city || data.address.town || data.address.village || data.address.county || data.address.state || 'Location found';
                                updateLocationText(locationName);
                            } else {
                                updateLocationText('Location found');
                            }
                        })
                        .catch(err => {
                            updateLocationText('Set Location');
                        });
                }, function (error) {
                    updateLocationText('Location denied');
                });
            } else {
                updateLocationText('Location unsupported');
            }
        });
    </script>

    @auth
    <script>
        // Logged in somewhere else? Tell the user on the spot and sign this browser out.
        (function () {
            var url = "{{ route('customer.session.check') }}";
            var shown = false;
            function leave() {
                try { localStorage.removeItem('auth_token'); localStorage.removeItem('user_data'); } catch (e) {}
                window.location.href = "{{ url('/customer/login') }}?replaced=1";
            }
            function popup(msg) {
                if (shown) return; shown = true;
                var box = document.createElement('div');
                box.className = 'sd-session-backdrop';
                box.innerHTML = '<div class="sd-session-card" role="alertdialog" aria-modal="true">' +
                    '<div class="sd-session-ic"><i class="fa-solid fa-user-lock"></i></div>' +
                    '<h5>Signed out</h5><p></p><button type="button" class="btn btn-primary w-100">OK</button></div>';
                box.querySelector('p').textContent = msg;
                box.querySelector('button').addEventListener('click', leave);
                document.body.appendChild(box);
                setTimeout(leave, 20000);
            }
            async function check() {
                if (shown || document.hidden) return;
                try {
                    var r = await fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
                    if (r.status === 401 || r.status === 409) {
                        var d = {}; try { d = await r.json(); } catch (e) {}
                        if (d.code === 'session_replaced') popup(d.message || 'You have logged in from another device. You have been logged out of this session.');
                    }
                } catch (e) { /* offline: try again next time */ }
            }
            setInterval(check, 15000);
            document.addEventListener('visibilitychange', function () { if (!document.hidden) check(); });
        })();
    </script>
    @endauth
    @stack('scripts')
    <script>
        (function () {
            var l = document.getElementById('sdPageLoader');
            if (!l) return;
            var timer = null;
            var hide = function () { clearTimeout(timer); l.classList.add('is-hidden'); };
            // hide as soon as the HTML is ready (not after every image) and never longer than 1.2 s
            if (document.readyState !== 'loading') hide(); else document.addEventListener('DOMContentLoaded', hide);
            setTimeout(hide, 1200);
            window.addEventListener('pageshow', hide);
            document.addEventListener('click', function (e) {
                var a = e.target.closest && e.target.closest('a[href]');
                if (!a || a.target === '_blank' || e.ctrlKey || e.metaKey || e.shiftKey) return;
                var h = a.getAttribute('href') || '';
                if (!h || h.charAt(0) === '#' || /^(mailto:|tel:|javascript:)/i.test(h)) return;
                if (a.origin && a.origin !== location.origin) return;
                // only show for slow navigations (fast ones feel instant without a flash)
                timer = setTimeout(function () { l.classList.remove('is-hidden'); }, 350);
            });
        })();
    </script>

    <!-- Mobile Bottom Navigation Bar (Only visible on small screens, hidden on payment/checkout) -->
    @if((request()->is('customer*') || request()->is('/')) && !request()->is('customer/payment*'))
    <div class="bottom-nav d-flex d-lg-none">
        <a href="{{ route('customer.home') }}" class="nav-item {{ request()->routeIs('customer.home') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('customer.orders') }}" class="nav-item {{ request()->routeIs('customer.orders', 'customer.order.show') ? 'active' : '' }}">
            <i class="fa-solid fa-clipboard-list"></i>
            <span>My Orders</span>
        </a>
        <a href="{{ route('customer.orders.create') }}" class="nav-item nav-book" aria-label="Book now">
            <span class="nav-book-btn"><i class="fa-solid fa-plus"></i></span>
            <span class="nav-book-label">Book Now</span>
        </a>
        <a href="{{ route('customer.rewards') }}" class="nav-item {{ request()->routeIs('customer.rewards') ? 'active' : '' }}">
            <i class="fa-solid fa-gift"></i>
            <span>Rewards</span>
        </a>
        <a href="{{ route('customer.profile') }}" class="nav-item {{ request()->routeIs('customer.profile', 'customer.change-password', 'customer.payments', 'customer.faqs', 'customer.scrap_vehicles') || request()->is('page/*') ? 'active' : '' }}">
            <i class="fa-solid fa-user"></i>
            <span>Profile</span>
        </a>
    </div>
    
    <style>
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #ffffff;
            border-top: 1px solid #f1f5f9;
            justify-content: space-around;
            align-items: center;
            height: 68px;
            z-index: 1050;
            padding-bottom: env(safe-area-inset-bottom);
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.04);
        }

        .bottom-nav .nav-item {
            text-decoration: none;
            color: #64748b;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 0.74rem;
            font-weight: 500;
            transition: all 0.2s ease;
            width: 20%;
            position: relative;
        }

        .bottom-nav .nav-item i {
            font-size: 1.25rem;
            margin-bottom: 3px;
        }

        /* Big green centre "+ Book Now" button, raised above the bar */
        .bottom-nav .nav-book { overflow: visible; }
        .bottom-nav .nav-book-btn {
            width: 62px; height: 62px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #4A9A58, #3D8B4D);
            color: #fff; margin-top: -30px; border: 4px solid #fff;
            box-shadow: 0 8px 20px rgba(61, 139, 77, 0.45);
        }
        .bottom-nav .nav-book-btn i { font-size: 1.7rem; margin: 0; }
        .bottom-nav .nav-book-label { color: #3D8B4D; font-weight: 700; margin-top: 2px; }

        .bottom-nav .nav-item.active {
            color: #4A9A58;
            font-weight: 700;
        }

        .bottom-nav .nav-item.active::after {
            content: '';
            display: block;
            width: 5px;
            height: 5px;
            background-color: #4A9A58;
            border-radius: 50%;
            margin: 3px auto 0 auto;
        }
        
        /* Add padding to body to prevent content from hiding behind bottom nav on mobile/tablet */
        @media (max-width: 991px) {
            body {
                padding-bottom: 75px;
            }
        }
    </style>
    @endif
    <link rel="stylesheet" href="{{ asset('css/modern.css') }}?v=5">
    @include('partials.pwa-install')
</body>

</html>