<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Scrap Daddy</title>
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
            <h2 class="auth-title">Verify <span class="highlight-green">Account</span></h2>
            <p class="auth-subtitle">Enter the OTP sent to your registered phone number</p>

            <form id="verifyForm">
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

                <!-- Verify Button -->
                <button type="submit" class="btn-auth-submit mt-2" id="verifyBtn">Verify & Login</button>
            </form>
        </div>

        <div class="auth-bottom-text">
            Didn't receive code? <a href="javascript:void(0)" id="resendOtpBtn">Resend OTP</a>
            <div class="mt-2">
                <a href="{{ url('/customer/login') }}" class="text-muted" style="font-weight: 500; font-size: 0.88rem;">Back to Login</a>
            </div>
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
            <h2 class="auth-title text-start mb-1">Verify <span class="highlight-green">Account</span></h2>
            <p class="auth-subtitle text-start mb-4">Enter the OTP sent to your registered phone number</p>

            <form id="desktopVerifyForm">
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

                <button type="submit" class="btn-auth-submit mt-2" id="desktopVerifyBtn">Verify & Login</button>
            </form>

            <div class="auth-bottom-text text-start mt-4">
                Didn't receive code? <a href="javascript:void(0)" id="desktopResendOtpBtn">Resend OTP</a>
                <div class="mt-2">
                    <a href="{{ url('/customer/login') }}" class="text-muted" style="font-weight: 500; font-size: 0.88rem;">Back to Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Get login param from URL
    const urlParams = new URLSearchParams(window.location.search);
    const loginValue = urlParams.get('login') || urlParams.get('phone_number') || urlParams.get('phone');
    if (loginValue) {
        if(document.getElementById('loginInput')) document.getElementById('loginInput').value = loginValue;
        if(document.getElementById('loginLegacyInput')) document.getElementById('loginLegacyInput').value = loginValue;
        if(document.getElementById('desktopLoginInput')) document.getElementById('desktopLoginInput').value = loginValue;
        if(document.getElementById('desktopLoginLegacyInput')) document.getElementById('desktopLoginLegacyInput').value = loginValue;
    }

    async function handleVerify(formElement, btnElement, alertElement, successElement) {
        const formData = new FormData(formElement);
        const currentLogin = formData.get('phone_number') || loginValue;
        if (!formData.get('phone_number') && currentLogin) {
            formData.append('phone_number', currentLogin);
        }
        if (!formData.get('login') && currentLogin) {
            formData.append('login', currentLogin);
        }

        btnElement.disabled = true;
        btnElement.innerText = 'Verifying...';
        alertElement.classList.add('d-none');
        successElement.classList.add('d-none');

        try {
            const response = await fetch("{{ url('/api/customer/verify-otp') }}", {
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

                successElement.innerText = 'Account verified successfully! Redirecting...';
                successElement.classList.remove('d-none');

                setTimeout(() => {
                    window.location.href = "{{ url('/customer/home') }}";
                }, 1000);
            } else {
                let errorText = (data && data.message) ? data.message : 'Verification failed.';
                if (data && data.data && data.data.errors) {
                    errorText = Object.values(data.data.errors).map(err => Array.isArray(err) ? err.join(', ') : err).join('<br>');
                } else if (data && data.errors) {
                    errorText = Object.values(data.errors).map(err => Array.isArray(err) ? err.join(', ') : err).join('<br>');
                }
                alertElement.innerHTML = errorText;
                alertElement.classList.remove('d-none');
                btnElement.disabled = false;
                btnElement.innerText = 'Verify & Login';
            }
        } catch (error) {
            console.error('Verify OTP error:', error);
            alertElement.innerText = 'An unexpected error occurred. Please try again.';
            alertElement.classList.remove('d-none');
            btnElement.disabled = false;
            btnElement.innerText = 'Verify & Login';
        }
    }

    const mobileVerifyForm = document.getElementById('verifyForm');
    if (mobileVerifyForm) {
        mobileVerifyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleVerify(this, document.getElementById('verifyBtn'), document.getElementById('errorAlert'), document.getElementById('successAlert'));
        });
    }

    const desktopVerifyForm = document.getElementById('desktopVerifyForm');
    if (desktopVerifyForm) {
        desktopVerifyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleVerify(this, document.getElementById('desktopVerifyBtn'), document.getElementById('desktopErrorAlert'), document.getElementById('desktopSuccessAlert'));
        });
    }

    // Resend OTP Handler
    async function handleResend(alertElement, successElement) {
        if (!loginValue) {
            alertElement.innerText = 'Phone number missing. Please login again.';
            alertElement.classList.remove('d-none');
            return;
        }

        alertElement.classList.add('d-none');
        successElement.classList.add('d-none');

        const fd = new FormData();
        fd.append('phone_number', loginValue);
        fd.append('login', loginValue);

        try {
            const response = await fetch("{{ url('/api/customer/forgot-password') }}", {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: fd
            });
            const data = await response.json();
            if (data && data.status === 1) {
                successElement.innerText = 'A new OTP has been sent to your phone.';
                successElement.classList.remove('d-none');
            } else {
                alertElement.innerText = data.message || 'Failed to resend OTP.';
                alertElement.classList.remove('d-none');
            }
        } catch (err) {
            alertElement.innerText = 'Error resending OTP.';
            alertElement.classList.remove('d-none');
        }
    }

    const resendBtn = document.getElementById('resendOtpBtn');
    if (resendBtn) {
        resendBtn.addEventListener('click', function(e) {
            e.preventDefault();
            handleResend(document.getElementById('errorAlert'), document.getElementById('successAlert'));
        });
    }

    const desktopResendBtn = document.getElementById('desktopResendOtpBtn');
    if (desktopResendBtn) {
        desktopResendBtn.addEventListener('click', function(e) {
            e.preventDefault();
            handleResend(document.getElementById('desktopErrorAlert'), document.getElementById('desktopSuccessAlert'));
        });
    }
</script>
</body>
</html>
