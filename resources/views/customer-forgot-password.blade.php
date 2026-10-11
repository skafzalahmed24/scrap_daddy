<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Scrap Daddy</title>
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
            <h2 class="auth-title">Forgot <span class="highlight-green">Password</span></h2>
            <p class="auth-subtitle">We'll send a OTP to your registered contact</p>

            <form id="forgotForm">
                <div id="errorAlert" class="alert alert-danger auth-alert d-none"></div>
                <div id="successAlert" class="alert alert-success auth-alert d-none"></div>

                <!-- Phone Number -->
                <div class="custom-auth-input-group">
                    <span class="input-icon">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="tel" class="form-control-custom" name="phone_number" placeholder="Enter Phone Number" required autofocus>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-auth-submit mt-2" id="submitBtn">Submit</button>
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
            <h2 class="auth-title text-start mb-1">Forgot <span class="highlight-green">Password</span></h2>
            <p class="auth-subtitle text-start mb-4">We'll send a OTP to your registered contact</p>

            <form id="desktopForgotForm">
                <div id="desktopErrorAlert" class="alert alert-danger auth-alert d-none"></div>
                <div id="desktopSuccessAlert" class="alert alert-success auth-alert d-none"></div>

                <div class="custom-auth-input-group">
                    <span class="input-icon">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="tel" class="form-control-custom" name="phone_number" placeholder="Enter Phone Number" required>
                </div>

                <button type="submit" class="btn-auth-submit mt-2" id="desktopSubmitBtn">Submit</button>
            </form>

            <div class="auth-bottom-text text-start mt-4">
                Remember your password? <a href="{{ url('/customer/login') }}">Login</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    async function handleForgot(formElement, btnElement, alertElement, successElement) {
        const formData = new FormData(formElement);
        const phoneNumber = formData.get('phone_number');
        if (phoneNumber && !formData.get('login')) {
            formData.append('login', phoneNumber);
        }

        btnElement.disabled = true;
        btnElement.innerText = 'Submitting...';
        alertElement.classList.add('d-none');
        successElement.classList.add('d-none');

        try {
            const response = await fetch("{{ url('/api/customer/forgot-password') }}", {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (data && data.status === 1) {
                successElement.innerText = 'OTP sent successfully! Redirecting...';
                successElement.classList.remove('d-none');

                setTimeout(() => {
                    window.location.href = "{{ url('/customer/reset-password') }}?login=" + encodeURIComponent(phoneNumber);
                }, 1000);
            } else {
                let errorText = (data && data.message) ? data.message : 'Request failed.';
                if (data && data.data && data.data.errors) {
                    errorText = Object.values(data.data.errors).map(err => Array.isArray(err) ? err.join(', ') : err).join('<br>');
                } else if (data && data.errors) {
                    errorText = Object.values(data.errors).map(err => Array.isArray(err) ? err.join(', ') : err).join('<br>');
                }
                alertElement.innerHTML = errorText;
                alertElement.classList.remove('d-none');
                btnElement.disabled = false;
                btnElement.innerText = 'Submit';
            }
        } catch (error) {
            console.error('Forgot password error:', error);
            alertElement.innerText = 'An unexpected error occurred. Please try again.';
            alertElement.classList.remove('d-none');
            btnElement.disabled = false;
            btnElement.innerText = 'Submit';
        }
    }

    const mobileForgotForm = document.getElementById('forgotForm');
    if (mobileForgotForm) {
        mobileForgotForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleForgot(this, document.getElementById('submitBtn'), document.getElementById('errorAlert'), document.getElementById('successAlert'));
        });
    }

    const desktopForgotForm = document.getElementById('desktopForgotForm');
    if (desktopForgotForm) {
        desktopForgotForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleForgot(this, document.getElementById('desktopSubmitBtn'), document.getElementById('desktopErrorAlert'), document.getElementById('desktopSuccessAlert'));
        });
    }
</script>
</body>
</html>
