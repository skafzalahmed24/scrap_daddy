@extends('layouts.app')

@section('title', 'Profile Details - Scrap Daddy')

@push('styles')
    @include('partials.customer.styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <style>
        .img-container {
            max-height: 400px;
            width: 100%;
            margin-bottom: 15px;
        }
        .img-container img {
            display: block;
            max-width: 100%;
        }
    </style>
@endpush

@section('content')

<div class="dashboard-container">
    <div class="row g-4">
        
        @include('partials.customer.sidebar')

        <!-- Main Content -->
        <div class="col-xl-9 col-lg-9">
            @php
                $user = auth()->user();
            @endphp
            

            <!-- DESKTOP VIEW -->
            <div class="middle-card d-none d-lg-block">
                <!-- Hero Banner -->
                @include('partials.customer.hero-banner', [
                    'title' => 'Your Profile',
                    'subtitle' => 'Manage your basic information and address details below.'
                ])

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0" style="color: var(--primary-blue, #0d2b4d);"></h4>
                    <button class="btn text-white fw-bold shadow-sm px-4" style="background-color: var(--primary-green, #1b5e20); border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fa-solid fa-pen me-2"></i> Edit
                    </button>
                </div>

                @if(session('success'))
                    <div class="toast-container position-fixed top-0 end-0 p-3 mt-5" style="z-index: 1060;">
                        <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="6000">
                            <div class="d-flex">
                                <div class="toast-body fw-medium px-3 py-2" style="font-size: 0.95rem;">
                                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                                </div>
                                <button type="button" class="btn-close btn-close-white me-3 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="text-center mb-5 mt-4">
                    <div class="position-relative d-inline-block">
                        <img src="{{ $user->profile_image ? asset($user->profile_image) : 'https://img.freepik.com/free-psd/3d-illustration-person-with-sunglasses_23-2149436188.jpg?w=250' }}" alt="Profile" class="rounded-circle shadow" style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #fff;">
                        <button class="btn btn-sm text-white position-absolute bottom-0 end-0 rounded-circle shadow" style="background-color: var(--primary-green, #1b5e20); width: 36px; height: 36px;" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="fa-solid fa-camera"></i>
                        </button>
                    </div>
                    <h5 class="mt-3 fw-bold text-dark">{{ $user->full_name }}</h5>
                    <p class="text-muted"><i class="fa-solid fa-phone me-2 text-success"></i>{{ $user->phone_number }}</p>
                </div>

                <h6 class="fw-bold text-muted mb-3"><i class="fa-solid fa-address-card me-2 text-success"></i>Basic Information</h6>
                <div class="bg-light p-4 rounded-4 border border-light mb-4 shadow-sm">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase mb-1">Full Name</label>
                            <div class="fw-bold text-dark fs-6">{{ $user->full_name ?? 'Not provided' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase mb-1">Phone Number</label>
                            <div class="fw-bold text-dark fs-6">{{ $user->phone_number ?? 'Not provided' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase mb-1">Email Address</label>
                            <div class="fw-bold text-dark fs-6">{{ $user->email ?? 'Not provided' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold text-uppercase mb-1">Pin Code</label>
                            <div class="fw-bold text-dark fs-6">{{ $user->pin_code ?? 'Not provided' }}</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold text-uppercase mb-1">Location / Address</label>
                            <div class="fw-bold text-dark fs-6">{{ $user->location ?? 'Not provided' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MOBILE VIEW -->
            <div class="d-lg-none border-0 shadow-sm rounded-4 p-0 bg-transparent">
                <!-- Profile Header -->
                <div class="text-center mb-4 mt-4">
                    <div class="position-relative d-inline-block">
                        <img src="{{ $user->profile_image ? asset($user->profile_image) : 'https://img.freepik.com/free-psd/3d-illustration-person-with-sunglasses_23-2149436188.jpg?w=250' }}" alt="Profile" class="rounded-circle shadow-sm" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #fff;">
                        <button class="btn btn-sm text-white position-absolute bottom-0 end-0 rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="background-color: var(--primary-green, #1b5e20); width: 30px; height: 30px; border: 2px solid #fff;" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="fa-solid fa-camera" style="font-size: 0.75rem;"></i>
                        </button>
                    </div>
                    <h5 class="mt-3 fw-bold text-dark mb-1">{{ $user->full_name }}</h5>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">{{ $user->email ?? $user->phone_number }}</p>
                </div>

                <style>
                    .profile-menu-item {
                        display: flex;
                        align-items: center;
                        padding: 16px 20px;
                        background-color: #ffffff;
                        border-radius: 16px;
                        margin-bottom: 12px;
                        text-decoration: none;
                        color: #333;
                        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
                        border: 1px solid rgba(0,0,0,0.02);
                        transition: all 0.2s ease;
                    }
                    .profile-menu-item:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
                        color: #333;
                    }
                    .profile-icon-box {
                        width: 40px;
                        height: 40px;
                        border-radius: 10px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin-right: 16px;
                        font-size: 1.1rem;
                    }
                    .profile-menu-label {
                        font-weight: 500;
                        font-size: 0.95rem;
                        flex-grow: 1;
                    }
                    .profile-chevron {
                        color: #ccc;
                        font-size: 0.8rem;
                    }
                </style>

                <!-- Settings List -->
                <div>
                    
                    <a href="#" class="profile-menu-item" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <div class="profile-icon-box" style="background-color: rgba(66, 133, 244, 0.1); color: #4285f4;">
                            <i class="fa-regular fa-user"></i>
                        </div>
                        <span class="profile-menu-label">Edit Profile</span>
                        <i class="fa-solid fa-chevron-right profile-chevron"></i>
                    </a>

                    <a href="{{ route('customer.payments') }}" class="profile-menu-item">
                        <div class="profile-icon-box" style="background-color: rgba(46, 125, 50, 0.1); color: #2e7d32;">
                            <i class="fa-regular fa-credit-card"></i>
                        </div>
                        <span class="profile-menu-label">Payments</span>
                        <i class="fa-solid fa-chevron-right profile-chevron"></i>
                    </a>

                    <a href="{{ route('customer.change-password') }}" class="profile-menu-item">
                        <div class="profile-icon-box" style="background-color: rgba(66, 133, 244, 0.1); color: #4285f4;">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <span class="profile-menu-label">Change Password</span>
                        <i class="fa-solid fa-chevron-right profile-chevron"></i>
                    </a>

                    <a href="{{ route('page.show', 'help-and-support') }}" class="profile-menu-item">
                        <div class="profile-icon-box" style="background-color: rgba(251, 188, 4, 0.1); color: #fbbc04;">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        <span class="profile-menu-label">Help & Support</span>
                        <i class="fa-solid fa-chevron-right profile-chevron"></i>
                    </a>

                    <a href="{{ route('page.show', 'faqs') }}" class="profile-menu-item">
                        <div class="profile-icon-box" style="background-color: rgba(156, 39, 176, 0.1); color: #9c27b0;">
                            <i class="fa-regular fa-circle-question"></i>
                        </div>
                        <span class="profile-menu-label">Faq</span>
                        <i class="fa-solid fa-chevron-right profile-chevron"></i>
                    </a>

                    <a href="{{ route('page.show', 'terms-and-conditions') }}" class="profile-menu-item">
                        <div class="profile-icon-box" style="background-color: rgba(33, 150, 243, 0.1); color: #2196f3;">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                        <span class="profile-menu-label">Terms & Conditions</span>
                        <i class="fa-solid fa-chevron-right profile-chevron"></i>
                    </a>

                    <a href="{{ route('page.show', 'privacy-policy') }}" class="profile-menu-item">
                        <div class="profile-icon-box" style="background-color: rgba(0, 188, 212, 0.1); color: #00bcd4;">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <span class="profile-menu-label">Privacy Policy</span>
                        <i class="fa-solid fa-chevron-right profile-chevron"></i>
                    </a>

                    <a href="#" onclick="event.preventDefault(); confirmDeleteAccount();" class="profile-menu-item">
                        <div class="profile-icon-box" style="background-color: rgba(244, 67, 54, 0.1); color: #f44336;">
                            <i class="fa-regular fa-trash-can"></i>
                        </div>
                        <span class="profile-menu-label">Delete Account</span>
                        <i class="fa-solid fa-chevron-right profile-chevron"></i>
                    </a>

                    <a href="#" onclick="event.preventDefault(); logoutCustomer();" class="profile-menu-item">
                        <div class="profile-icon-box" style="background-color: rgba(103, 58, 183, 0.1); color: #673ab7;">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </div>
                        <span class="profile-menu-label">Logout</span>
                        <i class="fa-solid fa-chevron-right profile-chevron"></i>
                    </a>
                </div>

        <!-- Right Sidebar Removed -->
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('customer.profile.update') }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            @csrf
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="editProfileModalLabel" style="color: var(--primary-blue, #0d2b4d);">Edit Profile</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3 text-center">
                    <label class="form-label fw-bold text-muted small text-uppercase w-100 text-start">Profile Image</label>
                    <input type="file" name="profile_image" id="profileImageInput" class="form-control rounded-3 py-2" accept="image/*">
                </div>
                <div class="mb-3 d-none" id="cropContainer">
                    <label class="form-label fw-bold text-muted small text-uppercase w-100 text-start">Crop Image</label>
                    <div class="img-container rounded-3 overflow-hidden border">
                        <img id="imageToCrop" src="" alt="Picture">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small text-uppercase">Full Name</label>
                    <input type="text" name="full_name" class="form-control rounded-3 py-2" value="{{ $user->full_name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small text-uppercase">Phone Number</label>
                    <input type="text" name="phone_number" class="form-control rounded-3 py-2" value="{{ $user->phone_number }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small text-uppercase">Email Address</label>
                    <input type="email" name="email" class="form-control rounded-3 py-2" value="{{ $user->email }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small text-uppercase">Pin Code</label>
                    <input type="text" name="pin_code" class="form-control rounded-3 py-2" value="{{ $user->pin_code }}">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small text-uppercase">Location / Address</label>
                    <textarea name="location" class="form-control rounded-3 py-2" rows="3">{{ $user->location }}</textarea>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn text-white rounded-pill px-4 fw-bold shadow-sm" style="background-color: var(--primary-green, #1b5e20);">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var toastEl = document.getElementById('successToast');
        if (toastEl) {
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        }

        const imageInput = document.getElementById('profileImageInput');
        const cropContainer = document.getElementById('cropContainer');
        const imageToCrop = document.getElementById('imageToCrop');
        let cropper;

        if (imageInput) {
            imageInput.addEventListener('change', function (e) {
                const files = e.target.files;
                const done = function (url) {
                    imageToCrop.src = url;
                    cropContainer.classList.remove('d-none');
                    if (cropper) {
                        cropper.destroy();
                    }
                    cropper = new Cropper(imageToCrop, {
                        aspectRatio: 1,
                        viewMode: 1,
                    });
                };
                let reader;
                let file;
                if (files && files.length > 0) {
                    file = files[0];
                    if (URL) {
                        done(URL.createObjectURL(file));
                    } else if (FileReader) {
                        reader = new FileReader();
                        reader.onload = function (e) {
                            done(reader.result);
                        };
                        reader.readAsDataURL(file);
                    }
                }
            });
        }

        const editForm = document.querySelector('#editProfileModal form');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(editForm);
                const token = localStorage.getItem('auth_token');
                
                if (!token) {
                    alert('Please log in again.');
                    window.location.href = '/customer/login';
                    return;
                }

                const submitBtn = editForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = 'Saving...';
                submitBtn.disabled = true;

                function submitForm(data) {
                    fetch('/api/customer/update-profile', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': 'Bearer ' + token
                        },
                        body: data
                    })
                    .then(response => response.json())
                    .then(data => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                        
                        if (data.status === 1) {
                            alert('Profile updated successfully!');
                            window.location.reload();
                        } else {
                            if (data.errors) {
                                let errorMsgs = [];
                                for (let field in data.errors) {
                                    errorMsgs.push(data.errors[field][0]);
                                }
                                alert(errorMsgs.join('\n'));
                            } else {
                                alert(data.message || 'Failed to update profile.');
                            }
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                        alert('An error occurred. Please try again.');
                    });
                }

                if (cropper) {
                    cropper.getCroppedCanvas({
                        width: 400,
                        height: 400,
                    }).toBlob((blob) => {
                        formData.set('profile_image', blob, 'profile.jpg');
                        submitForm(formData);
                    });
                } else {
                    submitForm(formData);
                }
            });
        }
    });

    function confirmDeleteAccount() {
        if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
            const token = localStorage.getItem('auth_token');
            fetch('/api/customer/delete-account', {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 1) {
                    localStorage.removeItem('auth_token');
                    alert(data.message);
                    window.location.href = '/customer/login';
                } else {
                    alert('Error: ' + (data.message || 'Could not delete account.'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred while deleting your account.');
            });
        }
    }
</script>
@endpush
