<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Scrap Daddy</title>
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
        <a href="{{ url('/customer/login') }}" class="auth-back-btn" title="Back to Login">
            <i class="bi bi-arrow-left"></i>
        </a>
        <img src="{{ asset('authimg.png') }}" onerror="if(this.src.indexOf('/public/')===-1) this.src='{{ asset('public/authimg.png') }}';" class="auth-banner-img" alt="Scrap Daddy">
    </div>

    <!-- Bottom Curved Form Sheet -->
    <div class="auth-card-sheet">
        <div>
            <h2 class="auth-title">Reset <span class="highlight-green">Password</span></h2>
            <p class="auth-subtitle">Enter the OTP and your new password</p>

            <form id="resetForm">
                <div id="errorAlert" class="alert alert-danger auth-alert d-none"></div>
                <div id="successAlert" class="alert alert-success auth-alert d-none"></div>

                <input type="hidden" id="loginInput" name="phone_number">
                <input type="hidden" id="loginLegacyInput" name="login">

                <!-- OTP Input -->
                <div class="custom-auth-input-group">
                    <span class="input-icon">
                        <i class="bi bi-shield-check"></i>
                    </span>
                    <input type="text" class="form-control-custom" name="otp" placeholder="Enter 6-digit OTP" required maxlength="6" inputmode="numeric" autofocus>
                </div>

                <!-- New Password -->
                <div class="custom-auth-input-group">
                    <span class="input-icon">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" class="form-control-custom" id="password" name="password" placeholder="Enter New Password" required>
                    <button class="password-toggle-btn" type="button" id="togglePassword" aria-label="Toggle password visibility">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>

                <!-- Confirm New Password -->
                <div class="custom-auth-input-group">
                    <span class="input-icon">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" class="form-control-custom" id="password_confirmation" name="password_confirmation" placeholder="Enter Confirm Password" required>
                    <button class="password-toggle-btn" type="button" id="toggleConfirmPassword" aria-label="Toggle confirm password visibility">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-auth-submit mt-3" id="resetBtn">Reset Password</button>
            </form>
        </div>

        <div class="auth-bottom-text">
            Remember your password? <a href="{{ url('/customer/login') }}">Login</a>
        </div>
    </div>
</div>

<!-- Desktop Split Layout (>= 768px) -->
<div class="desktop-auth-container">
    <div class="desktop-auth-image-side">
        <img src="{{ asset('authimg.png') }}" onerror="if(this.src.indexOf('/public/')===-1) this.src='{{ asset('public/authimg.png') }}';" alt="Scrap Daddy">
    </div>
    <div class="desktop-auth-form-side">
        <a href="{{ url('/customer/login') }}" class="auth-back-btn" title="Back to Login">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="desktop-auth-card-box">
            <h2 class="auth-title text-start mb-1">Reset <span class="highlight-green">Password</span></h2>
            <p class="auth-subtitle text-start mb-4">Enter the OTP and your new password</p>

            <form id="desktopResetForm">
                <div id="desktopErrorAlert" class="alert alert-danger auth-alert d-none"></div>
                <div id="desktopSuccessAlert" class="alert alert-success auth-alert d-none"></div>

                <input type="hidden" id="desktopLoginInput" name="phone_number">
                <input type="hidden" id="desktopLoginLegacyInput" name="login">

                <div class="custom-auth-input-group">
                    <span class="input-icon">
                        <i class="bi bi-shield-check"></i>
                    </span>
                    <input type="text" class="form-control-custom" name="otp" placeholder="Enter 6-digit OTP" required maxlength="6" inputmode="numeric">
                </div>

                <div class="custom-auth-input-group">
                    <span class="input-icon">
                        <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" class="form-control-custom" id="desktopPassword" name="password" placeholder="Enter New Password" required>
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

                <button type="submit" class="btn-auth-submit mt-3" id="desktopResetBtn">Reset Password</button>
            </form>

            <div class="auth-bottom-text text-start mt-4">
                Remember your password? <a href="{{ url('/customer/login') }}">Login</a>
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

    // Get login param from URL
    const urlParams = new URLSearchParams(window.location.search);
    const loginValue = urlParams.get('login') || urlParams.get('phone_number') || urlParams.get('phone');
    if (loginValue) {
        if(document.getElementById('loginInput')) document.getElementById('loginInput').value = loginValue;
        if(document.getElementById('loginLegacyInput')) document.getElementById('loginLegacyInput').value = loginValue;
        if(document.getElementById('desktopLoginInput')) document.getElementById('desktopLoginInput').value = loginValue;
        if(document.getElementById('desktopLoginLegacyInput')) document.getElementById('desktopLoginLegacyInput').value = loginValue;
    }

    async function handleReset(formElement, btnElement, alertElement, successElement) {
        const formData = new FormData(formElement);
        const currentLogin = formData.get('phone_number') || loginValue;
        if (!formData.get('phone_number') && currentLogin) {
            formData.append('phone_number', currentLogin);
        }
        if (!formData.get('login') && currentLogin) {
            formData.append('login', currentLogin);
        }

        if (formData.get('password') !== formData.get('password_confirmation')) {
            alertElement.innerText = 'Passwords do not match.';
            alertElement.classList.remove('d-none');
            return;
        }

        btnElement.disabled = true;
        btnElement.innerText = 'Resetting...';
        alertElement.classList.add('d-none');
        successElement.classList.add('d-none');

        try {
            const response = await fetch("{{ url('/api/customer/reset-password') }}", {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (data && data.status === 1) {
                successElement.innerText = 'Password reset successfully! Redirecting to login...';
                successElement.classList.remove('d-none');

                setTimeout(() => {
                    window.location.href = "{{ url('/customer/login') }}";
                }, 1500);
            } else {
                let errorText = (data && data.message) ? data.message : 'Password reset failed.';
                if (data && data.data && data.data.errors) {
                    errorText = Object.values(data.data.errors).map(err => Array.isArray(err) ? err.join(', ') : err).join('<br>');
                } else if (data && data.errors) {
                    errorText = Object.values(data.errors).map(err => Array.isArray(err) ? err.join(', ') : err).join('<br>');
                }
                alertElement.innerHTML = errorText;
                alertElement.classList.remove('d-none');
                btnElement.disabled = false;
                btnElement.innerText = 'Reset Password';
            }
        } catch (error) {
            console.error('Reset password error:', error);
            alertElement.innerText = 'An unexpected error occurred. Please try again.';
            alertElement.classList.remove('d-none');
            btnElement.disabled = false;
            btnElement.innerText = 'Reset Password';
        }
    }

    const mobileResetForm = document.getElementById('resetForm');
    if (mobileResetForm) {
        mobileResetForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleReset(this, document.getElementById('resetBtn'), document.getElementById('errorAlert'), document.getElementById('successAlert'));
        });
    }

    const desktopResetForm = document.getElementById('desktopResetForm');
    if (desktopResetForm) {
        desktopResetForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleReset(this, document.getElementById('desktopResetBtn'), document.getElementById('desktopErrorAlert'), document.getElementById('desktopSuccessAlert'));
        });
    }
</script>
</body>
</html>
