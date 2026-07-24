<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Scrap Daddy</title>
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
        <img src="/customerlogin.png" alt="Reset Password">
    </div>
    
    <!-- Form Section -->
    <div class="split-form">
        <div class="form-content-wrapper">
            <h2 class="fw-bold mb-1">Reset <span class="text-primary">Password</span></h2>
            <p class="text-muted mb-4">Enter the OTP and your new password</p>

            <form id="resetForm">
                <div id="errorAlert" class="alert alert-danger d-none"></div>
                <div id="successAlert" class="alert alert-success d-none"></div>

                <input type="hidden" id="loginInput" name="login">

                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text text-muted">
                            <i class="bi bi-shield-check"></i>
                        </span>
                        <input type="text" class="form-control" name="otp" placeholder="Enter 6-digit OTP" required maxlength="6">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text text-muted">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="New Password" required>
                        <button class="btn border-0 bg-white text-muted" style="border-left: none; border-radius: 0 0.375rem 0.375rem 0;" type="button" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text text-muted">
                            <i class="bi bi-shield-lock"></i>
                        </span>
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm New Password" required>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary py-2 fw-semibold" id="resetBtn">Reset Password</button>
                </div>
            </form>

            <div class="text-center mt-4 text-muted">
                <a href="/customer/login" class="text-primary text-decoration-none fw-semibold">Back to Login</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('togglePassword').addEventListener('click', function (e) {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });

    // Get login param from URL
    const urlParams = new URLSearchParams(window.location.search);
    const loginValue = urlParams.get('login');
    if(loginValue) {
        document.getElementById('loginInput').value = loginValue;
    } else {
        window.location.href = '/customer/forgot-password';
    }

    document.getElementById('resetForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('resetBtn');
        const alertBox = document.getElementById('errorAlert');
        const successBox = document.getElementById('successAlert');
        const formData = new FormData(this);
        
        // Check password confirmation
        if (formData.get('password') !== formData.get('password_confirmation')) {
            alertBox.innerText = 'Passwords do not match.';
            alertBox.classList.remove('d-none');
            return;
        }

        btn.disabled = true;
        btn.innerText = 'Resetting...';
        alertBox.classList.add('d-none');
        successBox.classList.add('d-none');

        try {
            const response = await fetch('/api/customer/reset-password', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok || data.status === 1) {
                successBox.innerText = 'Password reset successfully! Redirecting to login...';
                successBox.classList.remove('d-none');
                
                setTimeout(() => {
                    window.location.href = '/customer/login';
                }, 1500);
            } else {
                let errorText = data.message || 'Password reset failed.';
                if (data.errors) {
                    errorText = Object.values(data.errors).map(err => err.join(', ')).join('<br>');
                }
                alertBox.innerHTML = errorText;
                alertBox.classList.remove('d-none');
                btn.disabled = false;
                btn.innerText = 'Reset Password';
            }
        } catch (error) {
            alertBox.innerText = 'An unexpected error occurred. Please try again.';
            alertBox.classList.remove('d-none');
            btn.disabled = false;
            btn.innerText = 'Reset Password';
        }
    });
</script>
</body>
</html>
