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
</body>

</html>