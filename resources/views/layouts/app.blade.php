<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Scrap Daddy - Turn Scrap into Cash')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

    <!-- Mobile Bottom Navigation Bar (Only visible on small screens, hidden on payment/checkout) -->
    @if((request()->is('customer*') || request()->is('/')) && !request()->is('customer/payment*'))
    <div class="bottom-nav d-flex d-lg-none">
        <a href="{{ route('customer.home') }}" class="nav-item {{ request()->routeIs('customer.home') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('customer.orders') }}" class="nav-item {{ request()->routeIs('customer.orders') ? 'active' : '' }}">
            <i class="fa-solid fa-clipboard-list"></i>
            <span>My Orders</span>
        </a>
        <a href="{{ route('customer.rewards') }}" class="nav-item {{ request()->routeIs('customer.rewards') ? 'active' : '' }}">
            <i class="fa-solid fa-trophy"></i>
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
            width: 25%;
            position: relative;
        }

        .bottom-nav .nav-item i {
            font-size: 1.25rem;
            margin-bottom: 3px;
        }

        .bottom-nav .nav-item.active {
            color: #2e7d32;
            font-weight: 700;
        }

        .bottom-nav .nav-item.active::after {
            content: '';
            display: block;
            width: 5px;
            height: 5px;
            background-color: #2e7d32;
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
</body>

</html>