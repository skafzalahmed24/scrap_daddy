<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Scrap Daddy</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-green: #377d22; /* Matched from the image */
            --dark-green: #2d661b;
            --text-dark: #1a1f24;
            --text-gray: #6b7280;
            --border-color: #d1d5db;
            --bg-input: #ffffff;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: #ffffff;
            height: 100vh;
            overflow: hidden;
            display: flex;
        }

        .login-wrapper {
            display: flex;
            width: 100%;
            height: 100vh;
        }

        /* 65% Image Section */
        .image-section {
            width: 65%;
            height: 100%;
        }

        .image-section img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* 35% Form Section */
        .form-section {
            width: 35%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 0 40px; /* Adjusted padding to match the image feeling */
            background: #ffffff;
            color: var(--text-dark);
            box-sizing: border-box;
        }

        .form-section .inner-form {
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
        }

        .form-section h2 {
            font-size: 2.4rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--text-dark);
        }

        .form-section h2 span {
            color: var(--primary-green);
        }

        .form-section p {
            color: var(--text-gray);
            margin-top: 0;
            margin-bottom: 35px;
            font-size: 1.05rem;
            font-weight: 400;
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group svg {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: #9ca3af;
        }

        .input-group .toggle-password {
            position: absolute;
            right: 16px;
            left: auto;
            cursor: pointer;
            color: #9ca3af;
        }

        .input-group .toggle-password:hover {
            color: var(--text-dark);
        }

        .input-group input {
            width: 100%;
            padding: 14px 20px 14px 45px;
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-dark);
            font-size: 1rem;
            outline: none;
            box-sizing: border-box;
            transition: all 0.2s ease;
        }

        .input-group input::placeholder {
            color: #9ca3af;
            font-weight: 400;
        }

        .input-group input:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(55, 125, 34, 0.1);
        }

        .options-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 0.95rem;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            color: var(--text-gray);
            cursor: pointer;
        }

        .checkbox-container input {
            margin-right: 8px;
            width: 18px;
            height: 18px;
            border-radius: 4px;
            border: 1px solid var(--border-color);
            cursor: pointer;
            accent-color: var(--primary-green);
        }

        .forgot-link {
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            padding: 14px;
            background: var(--primary-green);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-btn:hover {
            background: var(--dark-green);
        }

        .error-message {
            color: #ef4444;
            font-size: 0.9rem;
            margin-bottom: 15px;
            display: none;
            background: #fef2f2;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #fecaca;
        }

        /* Loader */
        .loader {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-left: 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .register-text {
            text-align: center;
            margin-top: 40px;
            font-size: 0.95rem;
            color: var(--text-gray);
        }

        .register-text a {
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 600;
        }

        .register-text a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .image-section { width: 50%; }
            .form-section { width: 50%; }
        }
        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
                overflow-y: auto;
            }
            .image-section, .form-section {
                width: 100%;
                height: 50vh;
            }
            .form-section {
                padding: 40px 20px;
            }
            .form-section .inner-form {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <!-- Image Section -->
        <div class="image-section">
            <img src="/adminloginsidenav.png" alt="Scrap Daddy Admin Login">
        </div>

        <!-- Form Section -->
        <div class="form-section">
            <div class="inner-form">
                <h2>Welcome <span>Admin!</span></h2>
                <p>Login to your account</p>

                <div id="errorBox" class="error-message"></div>

                <form id="loginForm">
                    <div class="input-group">
                        <!-- Email Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <input type="email" id="email" name="email" value="admin@scrapedaddy.com" placeholder="Email Address" required>
                    </div>
                    
                    <div class="input-group">
                        <!-- Lock Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <input type="password" id="password" name="password" value="Vzario@123" placeholder="Password" required>
                        <!-- Eye Icon for toggle (optional functionality can be added later) -->
                        <svg class="toggle-password" id="togglePassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>

                    <div class="options-group">
                        <label class="checkbox-container">
                            <input type="checkbox" name="remember" id="remember">
                            Remember me
                        </label>
                        <a href="#" class="forgot-link">Forgot Password?</a>
                    </div>

                    <button type="submit" class="login-btn" id="loginBtn">
                        <span>Login</span>
                        <div class="loader" id="btnLoader"></div>
                    </button>
                </form>

                <div class="register-text">
                    Don't have an account? <a href="#">Register</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password toggle logic
        document.getElementById('togglePassword').addEventListener('click', function (e) {
            const passwordInput = document.getElementById('password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                // Change icon to eye-off
                this.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
            } else {
                passwordInput.type = 'password';
                // Change icon back to eye
                this.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
            }
        });

        // AJAX Login logic
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorBox = document.getElementById('errorBox');
            const loginBtn = document.getElementById('loginBtn');
            const btnLoader = document.getElementById('btnLoader');
            
            // UI Reset
            errorBox.style.display = 'none';
            loginBtn.style.opacity = '0.8';
            loginBtn.style.pointerEvents = 'none';
            btnLoader.style.display = 'block';

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email, password })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    window.location.href = result.redirect;
                } else {
                    errorBox.textContent = result.message || 'Login failed. Please check your credentials.';
                    errorBox.style.display = 'block';
                }
            } catch (error) {
                errorBox.textContent = 'An error occurred. Please try again later.';
                errorBox.style.display = 'block';
            } finally {
                loginBtn.style.opacity = '1';
                loginBtn.style.pointerEvents = 'auto';
                btnLoader.style.display = 'none';
            }
        });
    </script>
</body>
</html>
