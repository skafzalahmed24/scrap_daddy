@extends('layouts.app')

@section('title', 'Profile Details - Scrap Daddy')

@push('styles')
    @include('partials.customer.styles')
@endpush

@section('content')

<div class="dashboard-container">
    <div class="row g-4">
        
        @include('partials.customer.sidebar')

        <!-- Main Content -->
        <div class="col-xl-9 col-lg-9">
            @php
                $user = auth()->user() ?? \App\Models\User::first() ?? new \App\Models\User(['full_name' => 'Shaik Afzal', 'phone_number' => '+91 9876543210']);
            @endphp
            
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('customer.orders.create') }}" class="btn btn-success px-4 py-2 shadow-sm rounded-pill fw-bold"><i class="fa-solid fa-plus me-2"></i>New Pickup</a>
            </div>

            <!-- Hero Banner -->
            @include('partials.customer.hero-banner', [
                'title' => 'Your Profile',
                'subtitle' => 'Manage your basic information and address details below.'
            ])

            <div class="middle-card">
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
                        <img src="https://img.freepik.com/free-psd/3d-illustration-person-with-sunglasses_23-2149436188.jpg?w=250" alt="Profile" class="rounded-circle shadow" style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #fff;">
                        <button class="btn btn-sm text-white position-absolute bottom-0 end-0 rounded-circle shadow" style="background-color: var(--primary-green, #1b5e20); width: 36px; height: 36px;">
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var toastEl = document.getElementById('successToast');
        if (toastEl) {
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
    });
</script>
@endpush
