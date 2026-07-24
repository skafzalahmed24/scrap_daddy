<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Scrap Daddy</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Customer Auth CSS -->
    <link rel="stylesheet" href="{{ asset('css/customer-auth.css') }}">
</head>
<body>

<div class="split-layout">
    <!-- Image Section -->
    <div class="split-image">
        <img src="/customerlogin.png" alt="Verify Account">
    </div>
    
    <!-- Form Section -->
    <div class="split-form">
        <div class="form-content-wrapper">
            <h2 class="fw-bold mb-1">Verify <span class="text-primary">Account</span></h2>
            <p class="text-muted mb-4">Enter the OTP sent to your email or phone</p>

            <form id="verifyForm">
                <div id="errorAlert" class="alert alert-danger d-none"></div>
                <div id="successAlert" class="alert alert-success d-none"></div>

                <input type="hidden" id="loginInput" name="login">

                <div class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text text-muted">
                            <i class="bi bi-shield-check"></i>
                        </span>
                        <input type="text" class="form-control" name="otp" placeholder="Enter 6-digit OTP" required maxlength="6">
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary py-2 fw-semibold" id="verifyBtn">Verify & Login</button>
                </div>
            </form>

            <div class="text-center mt-4 text-muted">
                Didn't receive code? <a href="#" id="resendBtn" class="text-primary text-decoration-none fw-semibold">Resend OTP</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Get login param from URL
    const urlParams = new URLSearchParams(window.location.search);
    const loginValue = urlParams.get('login');
    if(loginValue) {
        document.getElementById('loginInput').value = loginValue;
    } else {
        window.location.href = '/customer/login';
    }

    document.getElementById('verifyForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('verifyBtn');
        const alertBox = document.getElementById('errorAlert');
        const successBox = document.getElementById('successAlert');
        const formData = new FormData(this);
        
        btn.disabled = true;
        btn.innerText = 'Verifying...';
        alertBox.classList.add('d-none');
        successBox.classList.add('d-none');

        try {
            const response = await fetch('/api/customer/verify-otp', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok || data.status === 1) {
                // Success! Store token in localStorage
                if (data.data && data.data.access_token) {
                    localStorage.setItem('auth_token', data.data.access_token);
                    localStorage.setItem('user_data', JSON.stringify(data.data.user));
                }
                
                successBox.innerText = 'Account verified successfully! Redirecting...';
                successBox.classList.remove('d-none');
                
                setTimeout(() => {
                    window.location.href = '/customer/home';
                }, 1000);
            } else {
                let errorText = data.message || 'Verification failed.';
                if (data.errors) {
                    errorText = Object.values(data.errors).map(err => err.join(', ')).join('<br>');
                }
                alertBox.innerHTML = errorText;
                alertBox.classList.remove('d-none');
                btn.disabled = false;
                btn.innerText = 'Verify & Login';
            }
        } catch (error) {
            alertBox.innerText = 'An unexpected error occurred. Please try again.';
            alertBox.classList.remove('d-none');
            btn.disabled = false;
            btn.innerText = 'Verify & Login';
        }
    });
</script>
</body>
</html>
