@php
    $user = auth()->user();
@endphp
<!-- Mobile Offcanvas Sidebar -->
<div class="offcanvas offcanvas-start border-0" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
    <div class="offcanvas-header bg-white border-bottom shadow-sm">
        <h5 class="offcanvas-title fw-bold" id="sidebarOffcanvasLabel" style="color: var(--primary-blue, #0d2b4d);">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="sidebar-wrapper h-100 rounded-0 shadow-none border-0">
            <div class="sidebar-profile">
                <img src="{{ $user->profile_image ? asset($user->profile_image) : 'https://img.freepik.com/free-psd/3d-illustration-person-with-sunglasses_23-2149436188.jpg?w=250' }}" alt="Sample Avatar" class="rounded-circle border border-2 border-white shadow-sm" style="width: 50px; height: 50px; object-fit: cover; margin-right: 15px; flex-shrink: 0;">
                <div class="info">
                    <p style="margin: 0; font-size: 0.8rem; opacity: 0.9;">{{ $user->phone_number }}</p>
                    <h5 style="margin: 0; font-weight: 700; font-size: 1.1rem;">{{ $user->full_name }}</h5>
                </div>
            </div>
            <ul class="sidebar-nav-list">
                <li><a href="{{ route('customer.orders') }}" class="{{ request()->is('customer/orders') ? 'active' : '' }}"><i class="fa-solid fa-truck"></i> My Pickups</a></li>
                <li><a href="#"><i class="fa-solid fa-gift"></i> Rewards</a></li>
                <li><a href="{{ route('customer.profile') }}" class="{{ request()->is('customer/profile') ? 'active' : '' }}"><i class="fa-regular fa-user"></i> Profile Details</a></li>
                <li><a href="#"><i class="fa-solid fa-car-side"></i> Scrap Vehicles</a></li>
                <li><a href="{{ route('customer.payments') }}" class="{{ request()->is('customer/payments') ? 'active' : '' }}"><i class="fa-regular fa-credit-card"></i> Payments</a></li>
                <li><a href="{{ route('page.show', 'help-and-support') }}" class="{{ request()->is('page/help-and-support') ? 'active' : '' }}"><i class="fa-solid fa-headset"></i> Help & Support</a></li>
                <li><a href="{{ route('page.show', 'privacy-policy') }}" class="{{ request()->is('page/privacy-policy') ? 'active' : '' }}"><i class="fa-solid fa-shield-halved"></i> Privacy Policy</a></li>
                <li><a href="{{ route('page.show', 'terms-and-conditions') }}" class="{{ request()->is('page/terms-and-conditions') ? 'active' : '' }}"><i class="fa-regular fa-file-lines"></i> Terms & Conditions</a></li>
                <li><a href="#" onclick="event.preventDefault(); logoutCustomer();" class="text-danger mt-4"><i class="fa-solid fa-arrow-right-from-bracket text-danger"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Mobile Sidebar Toggle (Hidden because bottom nav is used) -->
<div class="col-12 d-none mb-2">
    <button class="btn w-100 fw-bold shadow-sm py-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas" style="border: 1px solid var(--primary-green, #1b5e20); color: var(--primary-green, #1b5e20); background: #fff;">
        <i class="fa-solid fa-bars me-2"></i> Menu
    </button>
</div>

<!-- Left Sidebar (Desktop only) -->
<div class="col-xl-3 col-lg-3 d-none d-lg-block">
    <div class="sidebar-wrapper sticky-top" style="top: 24px;">
        <div class="sidebar-profile">
            <img src="{{ $user->profile_image ? asset($user->profile_image) : 'https://img.freepik.com/free-psd/3d-illustration-person-with-sunglasses_23-2149436188.jpg?w=250' }}" alt="Sample Avatar" class="rounded-circle border border-2 border-white shadow-sm" style="width: 50px; height: 50px; object-fit: cover; margin-right: 15px; flex-shrink: 0;">
            <div class="info">
                <p style="margin: 0; font-size: 0.8rem; opacity: 0.9;">{{ $user->phone_number }}</p>
                <h5 style="margin: 0; font-weight: 700; font-size: 1.1rem;">{{ $user->full_name }}</h5>
            </div>
        </div>
        <ul class="sidebar-nav-list">
            <li><a href="{{ route('customer.orders') }}" class="{{ request()->is('customer/orders') ? 'active' : '' }}"><i class="fa-solid fa-truck"></i> My Pickups</a></li>
            <li><a href="#"><i class="fa-solid fa-gift"></i> Rewards</a></li>
            <li><a href="{{ route('customer.profile') }}" class="{{ request()->is('customer/profile') ? 'active' : '' }}"><i class="fa-regular fa-user"></i> Profile Details</a></li>
            <li><a href="#"><i class="fa-solid fa-car-side"></i> Scrap Vehicles</a></li>
            <li><a href="{{ route('customer.payments') }}" class="{{ request()->is('customer/payments') ? 'active' : '' }}"><i class="fa-regular fa-credit-card"></i> Payments</a></li>
            <li><a href="{{ route('page.show', 'help-and-support') }}" class="{{ request()->is('page/help-and-support') ? 'active' : '' }}"><i class="fa-solid fa-headset"></i> Help & Support</a></li>
            <li><a href="{{ route('page.show', 'privacy-policy') }}" class="{{ request()->is('page/privacy-policy') ? 'active' : '' }}"><i class="fa-solid fa-shield-halved"></i> Privacy Policy</a></li>
            <li><a href="{{ route('page.show', 'terms-and-conditions') }}" class="{{ request()->is('page/terms-and-conditions') ? 'active' : '' }}"><i class="fa-regular fa-file-lines"></i> Terms & Conditions</a></li>
            <li><a href="#" onclick="event.preventDefault(); logoutCustomer();" class="text-danger mt-4"><i class="fa-solid fa-arrow-right-from-bracket text-danger"></i> Logout</a></li>
        </ul>
    </div>
</div>
