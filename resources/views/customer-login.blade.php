<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login - Scrap Daddy</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon_new.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon_new.ico') }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Customer Auth CSS & Embedded Styles -->
    <link rel="stylesheet" href="{{ asset('css/customer-auth.css') }}?v={{ time() }}">
    @include('partials.customer.auth-styles')
</head>
<body>

<!-- Mobile & Tablet Layout -->
<div class="auth-mobile-wrapper">
    <!-- Top Banner with Illustration & Back Button -->
    <div class="auth-banner-header">
        <a href="{{ url('/') }}" class="auth-back-btn" title="Back to Home">
            <i class="bi bi-arrow-left"></i>
        </a>
        <img src="{{ asset('authimg.png') }}" onerror="if(this.src.indexOf('/public/')===-1) this.src='{{ asset('public/authimg.png') }}';" class="auth-banner-img" alt="Scrap Daddy">
    </div>

    <!-- Bottom Curved Form Sheet -->
    <div class="auth-card-sheet">
        <div>
            <h2 class="auth-title">Welcome <span class="highlight-green">Back!</span></h2>
            <p class="auth-subtitle">Login to your customer account</p>

            <form id="loginForm">
                <div id="errorAlert" class="alert alert-danger auth-alert d-none"></div>

                <!-- Phone Number -->
                <div class="custom-auth-input-group">
                    <span class="input-icon">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="tel" class="form-control-custom" name="phone_number" placeholder="Enter Phone Number" required autofocus>
                </div>

                <!-- Password -->
                <div class="custom-auth-input-group">
                    <span class="input-icon">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" class="form-control-custom" id="password" name="password" placeholder="Enter Password" required>
                    <button class="password-toggle-btn" type="button" id="togglePassword" aria-label="Toggle password visibility">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>

                <!-- Forgot Password -->
                <div class="auth-forgot-row">
                    <a href="{{ url('/customer/forgot-password') }}" class="auth-forgot-link">Forgot Password?</a>
                </div>

                <!-- Login Button -->
                <button type="submit" class="btn-auth-submit" id="loginBtn">Login</button>
            </form>
        </div>

        <div class="auth-bottom-text">
            Don't have an account? <a href="{{ url('/customer/register') }}">Register</a>
        </div>
    </div>
</div>

<!-- Desktop Split Layout (>= 768px) -->
<div class="desktop-auth-container">
    <div class="desktop-auth-image-side">
        <img src="{{ asset('authimg.png') }}" onerror="if(this.src.indexOf('/public/')===-1) this.src='{{ asset('public/authimg.png') }}';" alt="Scrap Daddy">
    </div>
    <div class="desktop-auth-form-side">
        <a href="{{ url('/') }}" class="auth-back-btn" title="Back to Home">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="desktop-auth-card-box">
            <h2 class="auth-title text-start mb-1">Welcome <span class="highlight-green">Back!</span></h2>
            <p class="auth-subtitle text-start mb-4">Login to your customer account</p>

            <form id="desktopLoginForm">
                <div id="desktopErrorAlert" class="alert alert-danger auth-alert d-none"></div>

                <div class="custom-auth-input-group">
                    <span class="input-icon">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="tel" class="form-control-custom" name="phone_number" placeholder="Enter Phone Number" required>
                </div>

                <div class="custom-auth-input-group">
                    <span class="input-icon">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" class="form-control-custom" id="desktopPassword" name="password" placeholder="Enter Password" required>
                    <button class="password-toggle-btn" type="button" id="desktopTogglePassword" aria-label="Toggle password visibility">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>

                <div class="auth-forgot-row">
                    <a href="{{ url('/customer/forgot-password') }}" class="auth-forgot-link">Forgot Password?</a>
                </div>

                <button type="submit" class="btn-auth-submit" id="desktopLoginBtn">Login</button>
            </form>

            <div class="auth-bottom-text text-start mt-4">
                Don't have an account? <a href="{{ url('/customer/register') }}">Register</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Password visibility toggles
    function setupPasswordToggle(btnId, inputId) {
        const btn = document.getElementById(btnId);
        const input = document.getElementById(inputId);
        if (!btn || !input) return;
        btn.addEventListener('click', function () {
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        });
    }

    setupPasswordToggle('togglePassword', 'password');
    setupPasswordToggle('desktopTogglePassword', 'desktopPassword');

    // Login Submission Handler
    async function handleLogin(formElement, btnElement, alertElement) {
        const formData = new FormData(formElement);
        formData.append('platform_type', 1);

        btnElement.disabled = true;
        btnElement.innerText = 'Logging in...';
        alertElement.classList.add('d-none');

        try {
            const response = await fetch("{{ url('/api/customer/login') }}", {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (data && data.status === 1) {
                if (data.data && data.data.access_token) {
                    localStorage.setItem('auth_token', data.data.access_token);
                    localStorage.setItem('user_data', JSON.stringify(data.data.user));
                }
                
                const urlParams = new URLSearchParams(window.location.search);
                const redirectUrl = urlParams.get('redirect');
                
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                } else {
                    window.location.href = "{{ url('/customer/home') }}";
                }
            } else {
                if (data && data.data && data.data.requires_verification) {
                    window.location.href = "{{ url('/customer/verify-otp') }}?login=" + encodeURIComponent(formData.get('phone_number'));
                    return;
                }
                let errorText = (data && data.message) ? data.message : 'Login failed.';
                if (data && data.data && data.data.errors) {
                    errorText = Object.values(data.data.errors).map(err => Array.isArray(err) ? err.join(', ') : err).join('<br>');
                } else if (data && data.errors) {
                    errorText = Object.values(data.errors).map(err => Array.isArray(err) ? err.join(', ') : err).join('<br>');
                }
                alertElement.innerHTML = errorText;
                alertElement.classList.remove('d-none');
            }
        } catch (error) {
            console.error('Login error:', error);
            alertElement.innerText = 'An unexpected error occurred. Please try again.';
            alertElement.classList.remove('d-none');
        } finally {
            btnElement.disabled = false;
            btnElement.innerText = 'Login';
        }
    }

    const mobileForm = document.getElementById('loginForm');
    if (mobileForm) {
        mobileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleLogin(this, document.getElementById('loginBtn'), document.getElementById('errorAlert'));
        });
    }

    const desktopForm = document.getElementById('desktopLoginForm');
    if (desktopForm) {
        desktopForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleLogin(this, document.getElementById('desktopLoginBtn'), document.getElementById('desktopErrorAlert'));
        });
    }
</script>
</body>
</html>
