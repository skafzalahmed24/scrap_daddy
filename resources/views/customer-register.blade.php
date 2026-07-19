<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Register - Scrap Daddy</title>
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
        <img src="/customerregister (2).png" alt="Customer Register">
    </div>
    
    <!-- Form Section -->
    <div class="split-form">
        <div class="form-content-wrapper">
            <h2 class="fw-bold mb-1">Create <span class="text-primary">Account!</span></h2>
            <p class="text-muted mb-4">Register as a new customer</p>

            <form id="registerForm">
                <div id="errorAlert" class="alert alert-danger d-none"></div>
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text text-muted">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" class="form-control" name="full_name" placeholder="Full Name" required>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text text-muted">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input type="email" class="form-control" name="email" placeholder="Email Address">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text text-muted">
                            <i class="bi bi-telephone"></i>
                        </span>
                        <input type="tel" class="form-control" name="phone_number" placeholder="Phone Number" required>
                    </div>
                </div>

                <div class="mb-3">
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

                <div class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text text-muted">
                            <i class="bi bi-shield-lock"></i>
                        </span>
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary py-2 fw-semibold" id="registerBtn">Register</button>
                </div>
            </form>

            <div class="text-center mt-4 text-muted">
                Already have an account? <a href="/customer/login" class="text-primary text-decoration-none fw-semibold">Login</a>
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

    document.getElementById('registerForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('registerBtn');
        const alertBox = document.getElementById('errorAlert');
        const formData = new FormData(this);
        
        // Add default web platform type
        formData.append('platform_type', 1);

        // Check password confirmation
        if (formData.get('password') !== formData.get('password_confirmation')) {
            alertBox.innerText = 'Passwords do not match.';
            alertBox.classList.remove('d-none');
            return;
        }

        btn.disabled = true;
        btn.innerText = 'Registering...';
        alertBox.classList.add('d-none');

        try {
            const response = await fetch('/api/customer/register', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                // Success! Store token in localStorage or cookie if needed, then redirect
                localStorage.setItem('auth_token', data.access_token);
                window.location.href = '/customer/login?registered=1';
            } else {
                let errorText = data.message || 'Registration failed.';
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
            btn.innerText = 'Register';
        }
    });
</script>
</body>
</html>
