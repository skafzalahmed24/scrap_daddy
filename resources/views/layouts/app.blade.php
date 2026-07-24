<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Scrap Daddy - Turn Scrap into Cash')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Common Header & Footer CSS -->
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">

    @stack('styles')
</head>

<body>

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

    @stack('scripts')

    <!-- Mobile Bottom Navigation Bar (Only visible on small screens) -->
    @if(request()->is('customer*') || request()->is('/'))
    <div class="bottom-nav d-flex d-lg-none">
        <a href="{{ route('customer.home') }}" class="nav-item {{ request()->routeIs('customer.home') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('customer.orders') }}" class="nav-item {{ request()->routeIs('customer.orders') ? 'active' : '' }}">
            <i class="fa-solid fa-list-ul"></i>
            <span>My Orders</span>
        </a>
        <a href="{{ route('customer.rewards') }}" class="nav-item {{ request()->routeIs('customer.rewards') ? 'active' : '' }}">
            <i class="fa-solid fa-gift"></i>
            <span>Rewards</span>
        </a>
        <a href="{{ route('customer.profile') }}" class="nav-item {{ request()->routeIs('customer.profile') ? 'active' : '' }}">
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
            border-top: 1px solid #eee;
            justify-content: space-around;
            align-items: center;
            height: 65px;
            z-index: 1050;
            padding-bottom: env(safe-area-inset-bottom);
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }

        .bottom-nav .nav-item {
            text-decoration: none;
            color: #6c757d;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 0.75rem;
            font-weight: 500;
            transition: color 0.2s;
            width: 25%;
        }

        .bottom-nav .nav-item i {
            font-size: 1.25rem;
            margin-bottom: 2px;
        }

        .bottom-nav .nav-item.active {
            color: var(--primary-green, #1b5e20);
        }
        
        /* Add padding to body to prevent content from hiding behind bottom nav on mobile/tablet */
        @media (max-width: 991px) {
            body {
                padding-bottom: 70px;
            }
        }
    </style>
    @endif
</body>

</html>