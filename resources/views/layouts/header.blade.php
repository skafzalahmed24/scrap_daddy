<!-- Hide entire header on mobile across the app -->
<div class="d-none d-lg-block">
<!-- Top Header -->
<div class="top-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-3 col-md-4 mb-3 mb-md-0">
                <a href="/" class="brand-logo" style="gap: 0;">
                    <img src="/scraplogo.jpeg" alt="Scrap Daddy Logo" style="height: 60px;">
                </a>
            </div>
            <div
                class="col-lg-4 ms-auto text-lg-end d-none d-custom-flex justify-content-lg-end justify-content-center align-items-center gap-3">
                <div class="header-actions" id="desktopAuthActions">
                    <a href="/customer/login" class="btn btn-header-outline"><i class="fa-regular fa-user me-1"></i>
                        Login</a>
                    <a href="/customer/register" class="btn btn-create-account"><i
                            class="fa-solid fa-user-plus me-1"></i> Register</a>
                </div>
                <div class="header-actions d-none" id="desktopUserActions">
                    <a href="/customer/home" class="btn btn-header-outline"><i class="fa-solid fa-gauge-high me-1"></i>
                        Dashboard</a>
                    <button onclick="logoutCustomer()" class="btn btn-create-account bg-danger border-danger"><i
                            class="fa-solid fa-right-from-bracket me-1"></i> Logout</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Secondary Nav -->
<nav class="navbar navbar-expand-custom secondary-nav">
    <div class="container">

        <!-- Mobile Logo (Left Side) -->
        <a href="/" class="brand-logo d-lg-none me-2" style="text-decoration: none;">
            <img src="/scraplogo.jpeg" alt="Scrap Daddy Logo" style="height: 40px;">
        </a>

        <div id="userLocationMobile" class="px-3 py-1 d-none"
             style="cursor: pointer; align-items: center; gap: 8px; font-weight: 600; color: var(--primary-blue); background: rgba(27,94,32,0.05); border-radius: 20px;">
            <i class="fa-solid fa-location-dot"></i>
            <span id="locationTextMobile"
                  style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Detecting
                location...</span>
        </div>

        <style>
            @media (max-width: 991px) {
                .navbar-expand-custom .navbar-toggler.mobile-hide {
                    display: none !important;
                }
            }
        </style>
        @if(!request()->is('customer*'))
        <button class="navbar-toggler ms-auto mobile-hide" type="button" data-bs-toggle="collapse"
            data-bs-target="#secondaryNavContent" aria-controls="secondaryNavContent" aria-expanded="false"
            aria-label="Toggle navigation" style="border-color: var(--primary-blue); padding: 4px 8px;">
            <i class="fa-solid fa-bars" style="color: var(--primary-blue);"></i>
        </button>
        @else
        <button class="navbar-toggler ms-auto d-none" type="button" aria-hidden="true"></button>
        @endif
        <div class="collapse navbar-collapse justify-content-between align-items-center mt-3 mt-custom-0"
            id="secondaryNavContent">

            <!-- Left: Location Badge & Navigation Links -->
            <div class="d-flex flex-column flex-custom-row align-items-start align-items-custom-center gap-4">
                <div id="userLocation" class="px-3 py-1 d-none d-custom-inline-flex"
                    style="cursor: pointer; align-items: center; gap: 8px; font-weight: 600; color: var(--primary-blue); background: rgba(27,94,32,0.05); border-radius: 20px;">
                    <i class="fa-solid fa-location-dot"></i>
                    <span id="locationText"
                        style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Detecting
                        location...</span>
                </div>

                <div class="nav-links d-flex flex-column flex-custom-row align-items-start align-items-custom-center gap-3 gap-custom-0"
                    style="margin-left: 0; padding-left: 15px;">
                    <a href="/">Home</a>
                    <a href="/explore-categories">All Categories</a>
                    <a href="/#services">Our Services</a>
                    <a href="/#testimonials">Testimonials</a>
                </div>
            </div>

            <!-- Right: Contact Info -->
            <div
                class="nav-contact-info d-flex flex-column flex-custom-row gap-3 gap-custom-4 text-nowrap mt-4 mt-custom-0">
                <span><i class="fa-solid fa-phone-volume"></i> 1800-SCRAP-NOW</span>
                <span><i class="fa-regular fa-envelope"></i> support@scrapdaddy.com</span>
            </div>

            <!-- Mobile Auth Buttons -->
            <div class="d-custom-none mt-4 pb-2">
                <div class="d-flex flex-column gap-3" id="mobileAuthActions">
                    <a href="/customer/login" class="btn btn-header-outline w-100 text-center"><i
                            class="fa-regular fa-user me-1"></i> Login</a>
                    <a href="/customer/register" class="btn btn-create-account w-100 text-center"><i
                            class="fa-solid fa-user-plus me-1"></i> Register</a>
                </div>
                <div class="d-flex flex-column gap-3 d-none" id="mobileUserActions">
                    <a href="/customer/home" class="btn btn-header-outline w-100 text-center"><i
                            class="fa-solid fa-gauge-high me-1"></i> Dashboard</a>
                    <button onclick="logoutCustomer()"
                        class="btn btn-create-account w-100 text-center bg-danger border-danger"><i
                            class="fa-solid fa-right-from-bracket me-1"></i> Logout</button>
                </div>
            </div>

        </div>
    </div>
</nav>

<!-- Auth State Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const token = localStorage.getItem('auth_token');
        const desktopAuth = document.getElementById('desktopAuthActions');
        const desktopUser = document.getElementById('desktopUserActions');
        const mobileAuth = document.getElementById('mobileAuthActions');
        const mobileUser = document.getElementById('mobileUserActions');

        if (token) {
            // User is logged in
            if (desktopAuth) {
                desktopAuth.classList.add('d-none');
                desktopAuth.classList.remove('d-flex');
            }
            if (desktopUser) {
                desktopUser.classList.remove('d-none');
                // if it needs flex, add it here, but it doesn't have d-flex initially
            }
            if (mobileAuth) {
                mobileAuth.classList.add('d-none');
                mobileAuth.classList.remove('d-flex');
            }
            if (mobileUser) {
                mobileUser.classList.remove('d-none');
                mobileUser.classList.add('d-flex');
            }
        }
    });

    async function logoutCustomer() {
        const token = localStorage.getItem('auth_token');
        if (token) {
            try {
                await fetch('/api/customer/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
            } catch (e) {
                console.error('Logout error:', e);
            }
        }
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user_data');
        window.location.href = '/customer/logout-web';
    }
</script>