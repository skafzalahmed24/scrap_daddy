<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login - Scrap Daddy</title>
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
        <img src="/customerlogin.png" alt="Customer Login">
    </div>
    
    <!-- Form Section -->
    <div class="split-form">
        <div class="form-content-wrapper">
            <h2 class="fw-bold mb-1">Welcome <span class="text-primary">Back!</span></h2>
            <p class="text-muted mb-4">Login to your customer account</p>

            <form id="loginForm">
                <div id="errorAlert" class="alert alert-danger d-none"></div>
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text text-muted">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" class="form-control" name="login" placeholder="Phone Number or Email" required>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text text-muted">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <button class="btn border-0 bg-white text-muted" style="border-left: none; border-radius: 0 0.375rem 0.375rem 0;" type="button" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="remember">
                        <label class="form-check-label text-muted" for="remember">
                            Remember me
                        </label>
                    </div>
                    <a href="/customer/forgot-password" class="text-primary text-decoration-none fw-medium">Forgot Password?</a>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary py-2 fw-semibold" id="loginBtn">Login</button>
                </div>
            </form>

            <div class="text-center mt-4 text-muted">
                Don't have an account? <a href="/customer/register" class="text-primary text-decoration-none fw-semibold">Register</a>
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

    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('loginBtn');
        const alertBox = document.getElementById('errorAlert');
        const formData = new FormData(this);
        
        // Add platform type 1 for web
        formData.append('platform_type', 1);

        btn.disabled = true;
        btn.innerText = 'Logging in...';
        alertBox.classList.add('d-none');

        try {
            const response = await fetch('/api/customer/login', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok || data.status === 1) {
                // Success! Store token in localStorage
                localStorage.setItem('auth_token', data.data.access_token);
                localStorage.setItem('user_data', JSON.stringify(data.data.user));
                
                // Check for redirect query parameter
                const urlParams = new URLSearchParams(window.location.search);
                const redirectUrl = urlParams.get('redirect');
                
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                } else {
                    // Redirect to home/dashboard screen
                    window.location.href = '/customer/home';
                }
            } else {
                if (data.data && data.data.requires_verification) {
                    window.location.href = `/customer/verify-otp?login=${encodeURIComponent(formData.get('login'))}`;
                    return;
                }
                let errorText = data.message || 'Login failed.';
                if (data.errors) {
                    errorText = Object.values(data.errors).map(err => err.join(', ')).join('<br>');
                }
                alertBox.innerHTML = errorText;
                alertBox.classList.remove('d-none');
            }
        } catch (error) {
            alertBox.innerText = 'An unexpected error occurred. Please try again.';
            alertBox.classList.remove('d-none');
        } finally {
            btn.disabled = false;
            btn.innerText = 'Login';
        }
    });
</script>
</body>
</html>
