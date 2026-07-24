<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Scrap Daddy</title>
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
        <img src="/customerlogin.png" alt="Forgot Password">
    </div>
    
    <!-- Form Section -->
    <div class="split-form">
        <div class="form-content-wrapper">
            <h2 class="fw-bold mb-1">Forgot <span class="text-primary">Password?</span></h2>
            <p class="text-muted mb-4">Enter your phone or email to receive a reset OTP</p>

            <form id="forgotForm">
                <div id="errorAlert" class="alert alert-danger d-none"></div>
                <div id="successAlert" class="alert alert-success d-none"></div>

                <div class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text text-muted">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" class="form-control" name="login" placeholder="Phone Number or Email" required>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary py-2 fw-semibold" id="sendBtn">Send Reset OTP</button>
                </div>
            </form>

            <div class="text-center mt-4 text-muted">
                Remember your password? <a href="/customer/login" class="text-primary text-decoration-none fw-semibold">Login</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('forgotForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('sendBtn');
        const alertBox = document.getElementById('errorAlert');
        const successBox = document.getElementById('successAlert');
        const formData = new FormData(this);
        
        btn.disabled = true;
        btn.innerText = 'Sending...';
        alertBox.classList.add('d-none');
        successBox.classList.add('d-none');

        try {
            const response = await fetch('/api/customer/forgot-password', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok || data.status === 1) {
                successBox.innerText = 'OTP sent successfully! Redirecting...';
                successBox.classList.remove('d-none');
                
                setTimeout(() => {
                    window.location.href = `/customer/reset-password?login=${encodeURIComponent(formData.get('login'))}`;
                }, 1000);
            } else {
                let errorText = data.message || 'Request failed.';
                if (data.errors) {
                    errorText = Object.values(data.errors).map(err => err.join(', ')).join('<br>');
                }
                alertBox.innerHTML = errorText;
                alertBox.classList.remove('d-none');
                btn.disabled = false;
                btn.innerText = 'Send Reset OTP';
            }
        } catch (error) {
            alertBox.innerText = 'An unexpected error occurred. Please try again.';
            alertBox.classList.remove('d-none');
            btn.disabled = false;
            btn.innerText = 'Send Reset OTP';
        }
    });
</script>
</body>
</html>
