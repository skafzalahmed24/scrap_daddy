<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Register - Scrap Daddy</title>
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
            <h2 class="auth-title">Create <span class="highlight-green">Account!</span></h2>
            <p class="auth-subtitle">Register as a new customer</p>

            <form id="registerForm">
                <div id="errorAlert" class="alert alert-danger auth-alert d-none"></div>

                <!-- Full Name -->
                <div class="custom-auth-input-group">
                    <span class="input-icon">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" class="form-control-custom" name="full_name" placeholder="Enter Full Name" required autofocus>
                </div>

                <!-- Phone Number -->
                <div class="custom-auth-input-group">
                    <span class="input-icon">
                        <i class="bi bi-telephone"></i>
                    </span>
                    <input type="tel" class="form-control-custom" name="phone_number" placeholder="Enter Phone Number" required>
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

                <!-- Confirm Password -->
                <div class="custom-auth-input-group">
                    <span class="input-icon">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" class="form-control-custom" id="password_confirmation" name="password_confirmation" placeholder="Enter Confirm Password" required>
                    <button class="password-toggle-btn" type="button" id="toggleConfirmPassword" aria-label="Toggle confirm password visibility">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>

                <!-- Register Button -->
                <button type="submit" class="btn-auth-submit mt-3" id="registerBtn">Register</button>
            </form>
        </div>

        <div class="auth-bottom-text">
            Already have an account? <a href="{{ url('/customer/login') }}">Login</a>
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
            <h2 class="auth-title text-start mb-1">Create <span class="highlight-green">Account!</span></h2>
            <p class="auth-subtitle text-start mb-4">Register as a new customer</p>

            <form id="desktopRegisterForm">
                <div id="desktopErrorAlert" class="alert alert-danger auth-alert d-none"></div>

                <div class="custom-auth-input-group">
                    <span class="input-icon">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" class="form-control-custom" name="full_name" placeholder="Enter Full Name" required>
                </div>

                <div class="custom-auth-input-group">
                    <span class="input-icon">
                        <i class="bi bi-telephone"></i>
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

                <div class="custom-auth-input-group">
                    <span class="input-icon">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" class="form-control-custom" id="desktopPasswordConfirmation" name="password_confirmation" placeholder="Enter Confirm Password" required>
                    <button class="password-toggle-btn" type="button" id="desktopToggleConfirmPassword" aria-label="Toggle confirm password visibility">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>

                <button type="submit" class="btn-auth-submit mt-3" id="desktopRegisterBtn">Register</button>
            </form>

            <div class="auth-bottom-text text-start mt-4">
                Already have an account? <a href="{{ url('/customer/login') }}">Login</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
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
    setupPasswordToggle('toggleConfirmPassword', 'password_confirmation');
    setupPasswordToggle('desktopTogglePassword', 'desktopPassword');
    setupPasswordToggle('desktopToggleConfirmPassword', 'desktopPasswordConfirmation');

    // Register Handler
    async function handleRegister(formElement, btnElement, alertElement) {
        const formData = new FormData(formElement);
        formData.append('platform_type', 1);

        if (formData.get('password') !== formData.get('password_confirmation')) {
            alertElement.innerText = 'Passwords do not match.';
            alertElement.classList.remove('d-none');
            return;
        }

        btnElement.disabled = true;
        btnElement.innerText = 'Registering...';
        alertElement.classList.add('d-none');

        try {
            const response = await fetch("{{ url('/api/customer/register') }}", {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (data && data.status === 1) {
                window.location.href = "{{ url('/customer/verify-otp') }}?login=" + encodeURIComponent(formData.get('phone_number'));
            } else {
                let errorText = (data && data.message) ? data.message : 'Registration failed.';
                if (data && data.data && data.data.errors) {
                    errorText = Object.values(data.data.errors).map(err => Array.isArray(err) ? err.join(', ') : err).join('<br>');
                } else if (data && data.errors) {
                    errorText = Object.values(data.errors).map(err => Array.isArray(err) ? err.join(', ') : err).join('<br>');
                }
                alertElement.innerHTML = errorText;
                alertElement.classList.remove('d-none');
            }
        } catch (error) {
            console.error('Registration error:', error);
            alertElement.innerText = 'An unexpected error occurred. Please try again.';
            alertElement.classList.remove('d-none');
        } finally {
            btnElement.disabled = false;
            btnElement.innerText = 'Register';
        }
    }

    const mobileRegisterForm = document.getElementById('registerForm');
    if (mobileRegisterForm) {
        mobileRegisterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleRegister(this, document.getElementById('registerBtn'), document.getElementById('errorAlert'));
        });
    }

    const desktopRegisterForm = document.getElementById('desktopRegisterForm');
    if (desktopRegisterForm) {
        desktopRegisterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleRegister(this, document.getElementById('desktopRegisterBtn'), document.getElementById('desktopErrorAlert'));
        });
    }
</script>
</body>
</html>
