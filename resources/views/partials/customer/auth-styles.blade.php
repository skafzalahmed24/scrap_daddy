<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

:root {
    --primary-green: #1b5e20;
    --accent-green: #2e7d32;
    --btn-grey: #7f8c8d;
    --input-border: #ced4da;
    --text-dark: #222222;
    --text-muted: #6c757d;
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body, html {
    min-height: 100%;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background-color: #ffffff;
    color: var(--text-dark);
}

/* Auth Floating Back Button */
.auth-back-btn {
    position: absolute;
    top: 18px;
    left: 18px;
    width: 42px;
    height: 42px;
    background-color: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #222222;
    font-size: 1.25rem;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    transition: all 0.25s ease;
    z-index: 50;
}

.auth-back-btn:hover {
    background-color: #f1f8f1;
    color: var(--primary-green);
    transform: translateX(-3px);
}

/* Mobile & Base Layout (< 768px) */
.auth-mobile-wrapper {
    width: 100%;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background-color: #ffffff;
    position: relative;
    max-width: 480px;
    margin: 0 auto;
}

.auth-banner-header {
    position: relative;
    width: 100%;
    height: 310px;
    background: #eef7ed;
    overflow: hidden;
}

.auth-banner-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center 15%;
    display: block;
}

.auth-card-sheet {
    background: #ffffff;
    border-top-left-radius: 35px;
    border-top-right-radius: 35px;
    margin-top: -38px;
    position: relative;
    z-index: 10;
    padding: 32px 24px 36px 24px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    box-shadow: 0 -8px 25px rgba(0, 0, 0, 0.04);
}

/* Typography */
.auth-title {
    font-size: 1.65rem;
    font-weight: 800;
    text-align: center;
    color: var(--text-dark);
    margin-bottom: 4px;
    letter-spacing: -0.3px;
}

.auth-title .highlight-green {
    color: var(--primary-green);
}

.auth-subtitle {
    font-size: 0.92rem;
    text-align: center;
    color: var(--text-muted);
    margin-bottom: 24px;
    font-weight: 500;
}

/* Custom Input Groups matching the reference UI */
.custom-auth-input-group {
    display: flex;
    align-items: center;
    background: #ffffff;
    border: 1.5px solid var(--input-border);
    border-radius: 12px;
    height: 52px;
    padding: 0 16px;
    margin-bottom: 16px;
    transition: all 0.2s ease;
    position: relative;
}

.custom-auth-input-group:focus-within {
    border-color: var(--accent-green);
    box-shadow: 0 0 0 3.5px rgba(46, 125, 50, 0.12);
}

.custom-auth-input-group .input-icon {
    color: var(--accent-green);
    font-size: 1.25rem;
    margin-right: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.custom-auth-input-group .form-control-custom {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    width: 100%;
    font-size: 0.95rem;
    color: var(--text-dark);
    background: transparent;
    font-family: inherit;
    font-weight: 500;
    padding: 0;
}

.custom-auth-input-group .form-control-custom::placeholder {
    color: #9aa0a6;
    font-weight: 400;
}

.custom-auth-input-group .password-toggle-btn {
    background: transparent;
    border: none;
    color: #6c757d;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0;
    margin-left: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    outline: none;
}

.custom-auth-input-group .password-toggle-btn:hover {
    color: #333333;
}

/* Links & Buttons */
.auth-forgot-row {
    display: flex;
    justify-content: flex-end;
    margin-top: -4px;
    margin-bottom: 22px;
}

.auth-forgot-link {
    color: var(--primary-green);
    font-weight: 700;
    font-size: 0.92rem;
    text-decoration: none;
    transition: color 0.2s ease;
}

.auth-forgot-link:hover {
    color: var(--accent-green);
    text-decoration: underline;
}

.btn-auth-submit {
    width: 100%;
    height: 52px;
    border-radius: 12px;
    background-color: var(--btn-grey);
    border: none;
    color: #ffffff;
    font-weight: 700;
    font-size: 1.05rem;
    letter-spacing: 0.2px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.25s ease;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
}

.btn-auth-submit:hover,
.btn-auth-submit:active,
.btn-auth-submit.active-btn {
    background-color: var(--primary-green);
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(27, 94, 32, 0.25);
}

.auth-bottom-text {
    text-align: center;
    margin-top: 24px;
    color: #555555;
    font-size: 0.92rem;
    font-weight: 500;
}

.auth-bottom-text a {
    color: var(--primary-green);
    font-weight: 700;
    text-decoration: none;
    margin-left: 4px;
}

.auth-bottom-text a:hover {
    text-decoration: underline;
}

/* Alerts */
.auth-alert {
    border-radius: 10px;
    font-size: 0.88rem;
    padding: 10px 14px;
    margin-bottom: 16px;
}

/* Desktop Responsiveness (>= 768px) */
@media (min-width: 768px) {
    body, html {
        background-color: #f8fafc;
    }

    .desktop-auth-container {
        min-height: 100vh;
        display: flex;
        align-items: stretch;
    }

    .desktop-auth-image-side {
        flex: 1 1 55%;
        position: relative;
        background: #e8f5e9;
        overflow: hidden;
    }

    .desktop-auth-image-side img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        position: absolute;
        top: 0;
        left: 0;
    }

    .desktop-auth-form-side {
        flex: 1 1 45%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        background: #ffffff;
        position: relative;
    }

    .desktop-auth-card-box {
        width: 100%;
        max-width: 440px;
    }

    /* On desktop, hide mobile wrapper and show split container */
    .auth-mobile-wrapper {
        display: none !important;
    }
}

@media (max-width: 767.98px) {
    .desktop-auth-container {
        display: none !important;
    }
}
</style>
