@extends('layouts.app')

@section('title', 'Change Password - Scrap Daddy')

@push('styles')
    @include('partials.customer.styles')
@endpush

@section('content')

<div class="dashboard-container">
    <div class="row g-4">
        
        @include('partials.customer.sidebar')

        <!-- Main Content -->
        <div class="col-xl-9 col-lg-9">
            <!-- Hero Banner -->
            <div class="d-none d-lg-block">
                @include('partials.customer.hero-banner', [
                    'title' => 'Change Password',
                    'subtitle' => 'Update your password securely to keep your account safe.'
                ])
            </div>

            <!-- Mobile Header with Back Button -->
            <div class="d-flex align-items-center mb-4 d-lg-none bg-white p-3 rounded-4 shadow-sm border border-light">
                <a href="{{ route('customer.profile') }}" class="text-dark text-decoration-none d-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h5 class="mb-0 fw-bold ms-3" style="color: var(--primary-blue, #0d2b4d);">Change Password</h5>
            </div>

            <div class="middle-card bg-transparent border-0 shadow-none bg-lg-white shadow-lg-sm border-lg p-0 p-lg-4">
                <div class="bg-white rounded-4 shadow-sm border border-light p-4">
                    <form id="changePasswordForm">
                        <div class="mb-3 position-relative">
                            <label class="form-label fw-bold text-muted small text-uppercase">Old Password</label>
                            <input type="password" id="old_password" name="old_password" class="form-control rounded-3 py-2 pe-5" required placeholder="Enter current password">
                            <i class="fa-solid fa-eye position-absolute top-50 end-0 translate-middle-y me-3 mt-3 text-muted" style="cursor: pointer;" onclick="togglePassword('old_password', this)"></i>
                        </div>
                        
                        <div class="mb-3 position-relative">
                            <label class="form-label fw-bold text-muted small text-uppercase">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="form-control rounded-3 py-2 pe-5" required placeholder="Enter new password">
                            <i class="fa-solid fa-eye position-absolute top-50 end-0 translate-middle-y me-3 mt-3 text-muted" style="cursor: pointer;" onclick="togglePassword('new_password', this)"></i>
                        </div>
                        
                        <div class="mb-4 position-relative">
                            <label class="form-label fw-bold text-muted small text-uppercase">Confirm New Password</label>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control rounded-3 py-2 pe-5" required placeholder="Re-enter new password">
                            <i class="fa-solid fa-eye position-absolute top-50 end-0 translate-middle-y me-3 mt-3 text-muted" style="cursor: pointer;" onclick="togglePassword('new_password_confirmation', this)"></i>
                        </div>
                        
                        <button type="submit" class="btn text-white rounded-pill px-4 py-2 fw-bold w-100 shadow-sm" style="background-color: var(--primary-green, #1b5e20);">Update Password</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordForm = document.getElementById('changePasswordForm');
        
        if (passwordForm) {
            passwordForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const token = localStorage.getItem('auth_token');
                if (!token) {
                    alert('Please log in again.');
                    window.location.href = '/customer/login';
                    return;
                }

                const submitBtn = passwordForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = 'Updating...';
                submitBtn.disabled = true;

                const formData = new FormData(passwordForm);
                const data = Object.fromEntries(formData.entries());

                fetch('/api/customer/change-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(res => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    
                    if (res.status === 1) {
                        alert(res.message || 'Password changed successfully!');
                        passwordForm.reset();
                    } else {
                        if (res.errors) {
                            let errorMsgs = [];
                            for (let field in res.errors) {
                                errorMsgs.push(res.errors[field][0]);
                            }
                            alert(errorMsgs.join('\n'));
                        } else {
                            alert(res.message || 'Failed to change password.');
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    alert('An error occurred. Please try again.');
                });
            });
        }
    });

    function togglePassword(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endpush
