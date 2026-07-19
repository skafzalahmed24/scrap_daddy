@extends('layouts.app')

@section('title', $category->title . ' - Scrap Daddy')

@push('styles')

    <style>
        /* Mobile horizontal scroll for category sidebar */
        @media (max-width: 991px) {
            .category-sidebar-list {
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: wrap !important;
                max-height: none !important;
                padding-bottom: 0 !important;
                gap: 10px;
                justify-content: center;
            }
            .category-sidebar-list .list-group-item {
                border-bottom: 0 !important;
                border-left: 0 !important;
                border-bottom: 3px solid transparent !important;
            }
            .category-sidebar-list .list-group-item[style*="border-left"] {
                border-left: 0 !important;
                border-bottom: 3px solid var(--primary-green) !important;
            }
        }
        /* Hide scrollbar for webkit */
        .category-sidebar-list::-webkit-scrollbar {
            height: 4px;
        }
        .category-sidebar-list::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .category-sidebar-list::-webkit-scrollbar-thumb {
            background: #cdcdcd;
            border-radius: 4px;
        }
    </style>
@endpush

@section('content')

    <!-- Subcategories Section -->
    <section class="subcategories-section py-5 bg-light" style="min-height: 60vh;">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="section-heading mb-0 text-start">Category Explorer</h3>
                <a href="/" class="btn btn-outline-success rounded-pill px-4" style="border-color: var(--primary-green); color: var(--primary-green);"><i class="fa-solid fa-arrow-left me-1"></i> Back to Home</a>
            </div>
            
            <div class="row g-4">
                <!-- Sidebar: All Categories -->
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 mb-lg-0 sticky-lg-top" style="top: 20px; z-index: 10;">
                        <div class="card-header bg-white border-bottom-0 pt-4 pb-2 d-none d-lg-block">
                            <a href="/explore-categories" class="text-decoration-none">
                                <h5 class="fw-bold mb-0" style="color: var(--primary-blue);">
                                    <i class="fa-solid fa-layer-group me-2" style="font-size: 1rem;"></i>All Categories
                                </h5>
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush category-sidebar-list" style="max-height: 65vh; overflow-y: auto;">
                                @if(isset($allCategories))
                                    @foreach($allCategories as $cat)
                                    <a href="{{ route('category.show', $cat->uuid) }}" 
                                       class="list-group-item list-group-item-action border-0 px-4 py-3 d-flex align-items-center"
                                       style="{{ $cat->uuid === $category->uuid ? 'background-color: rgba(46,125,50,0.05); color: var(--primary-green); border-left: 4px solid var(--primary-green) !important; font-weight: 600;' : 'color: #555;' }}">
                                        @if($cat->image)
                                            <img src="/{{ $cat->image }}" alt="" style="width: 24px; height: 24px; object-fit: cover; border-radius: 4px; margin-right: 12px;">
                                        @else
                                            <i class="fa-solid fa-recycle me-3" style="{{ $cat->uuid === $category->uuid ? 'color: var(--primary-green);' : 'color: #aaa;' }}"></i>
                                        @endif
                                        {{ $cat->title }}
                                        @if($cat->uuid === $category->uuid)
                                            <i class="fa-solid fa-chevron-right ms-auto d-none d-lg-block" style="font-size: 0.8rem;"></i>
                                        @endif
                                    </a>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content: Subcategories Grid -->
                <div class="col-lg-9">
                    <div class="d-flex align-items-center mb-4">
                        @if($category->image)
                            <img src="/{{ $category->image }}" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 8px; margin-right: 15px;">
                        @endif
                        <h4 class="fw-bold mb-0" style="color: #333;">{{ $category->title }} Subcategories</h4>
                    </div>

                    <div class="row row-cols-3 row-cols-md-4 g-4 justify-content-center">
                        @if(isset($subcategories) && count($subcategories) > 0)
                            @foreach($subcategories as $sub)
                            <div class="col d-flex flex-column align-items-center">
                                <a href="javascript:void(0)" class="text-decoration-none text-center subcategory-item" data-id="{{ $sub->id }}" data-uuid="{{ $sub->uuid }}" data-name="{{ $sub->name }}">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm mx-auto mb-2" style="width: 100px; height: 100px; background-color: #ffffff; border: 2px solid var(--primary-green); overflow: hidden; transition: transform 0.3s; position: relative;">
                                        @if($sub->image)
                                            <img src="/{{ $sub->image }}" alt="{{ $sub->name }}" style="width: 100%; height: 100%; object-fit: contain; padding: 15px;">
                                        @else
                                            <i class="fa-solid fa-recycle fa-3x" style="color: var(--primary-green);"></i>
                                        @endif
                                    </div>
                                    <h6 class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $sub->name }}</h6>
                                </a>
                            </div>
                            @endforeach
                        @else
                            <div class="col-12 text-center text-muted mt-5 bg-white rounded-4 shadow-sm py-5 border-0">
                                <i class="fa-solid fa-box-open fa-3x mb-3" style="color: #ccc;"></i>
                                <h5>No subcategories found for {{ $category->title }}.</h5>
                                <p class="mb-0">Check back later!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Location Detection Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const locText = document.getElementById('locationText');
            const locTextMobile = document.getElementById('locationTextMobile');
            
            function updateLocationText(text) {
                if (locText) locText.innerText = text;
                if (locTextMobile) locTextMobile.innerText = text;
            }

            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    
                    // Reverse geocoding using free Nominatim API
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&zoom=10`)
                        .then(response => response.json())
                        .then(data => {
                            if(data && data.address) {
                                // Fallbacks for different location granularity
                                const locationName = data.address.city || data.address.town || data.address.village || data.address.county || data.address.state || 'Location found';
                                updateLocationText(locationName);
                            } else {
                                updateLocationText('Location found');
                            }
                        })
                        .catch(err => {
                            updateLocationText('Set Location');
                        });
                }, function(error) {
                    updateLocationText('Location denied');
                });
            } else {
                updateLocationText('Location unsupported');
            }
        });
    </script>
    <!-- Login Required Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center pb-5">
                    <i class="fa-solid fa-user-lock fa-4x text-warning mb-3"></i>
                    <h4 class="fw-bold mb-3">Login Required</h4>
                    <p class="text-muted mb-4">Please login or create an account to schedule a pickup for <strong id="loginSubcategoryName">this item</strong>.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="/customer/login" class="btn btn-primary px-4 py-2 rounded-pill" style="background-color: var(--primary-blue); border: none;"><i class="fa-solid fa-right-to-bracket me-2"></i>Login</a>
                        <a href="/customer/register" class="btn btn-outline-success px-4 py-2 rounded-pill" style="color: var(--primary-green); border-color: var(--primary-green);"><i class="fa-solid fa-user-plus me-2"></i>Sign Up</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Multi-Step Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header bg-light border-0 py-3 rounded-top-4">
                    <h5 class="modal-title fw-bold" id="bookingModalLabel"><i class="fa-solid fa-calendar-check me-2" style="color: var(--primary-green);"></i> Schedule Pickup: <span id="bookingSubcategoryName" style="color: var(--primary-blue);"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Progress Bar -->
                    <div class="position-relative mb-5 mx-3">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar" id="bookingProgress" role="progressbar" style="width: 33%; background-color: var(--primary-green);" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between position-absolute w-100 top-50 start-0 translate-middle-y">
                            <div class="text-white rounded-circle d-flex align-items-center justify-content-center step-circle active-step" style="width: 30px; height: 30px; font-weight: bold; background-color: var(--primary-green);">1</div>
                            <div class="bg-light text-muted border rounded-circle d-flex align-items-center justify-content-center step-circle" id="stepIndicator2" style="width: 30px; height: 30px; font-weight: bold;">2</div>
                            <div class="bg-light text-muted border rounded-circle d-flex align-items-center justify-content-center step-circle" id="stepIndicator3" style="width: 30px; height: 30px; font-weight: bold;">3</div>
                        </div>
                    </div>

                    <form id="bookingForm">
                        <!-- Step 1: Schedule & Location -->
                        <div class="booking-step" id="step1">
                            <h5 class="fw-bold mb-4">Location & Date</h5>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Pickup Location</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text bg-white"><i class="fa-solid fa-location-dot text-danger"></i></span>
                                    <input type="text" class="form-control" id="pickupLocation" placeholder="Enter pickup address or use auto-detect">
                                    <button class="btn btn-outline-secondary" type="button" id="btnDetectLocation"><i class="fa-solid fa-crosshairs me-2"></i>Detect</button>
                                </div>
                                <small class="text-muted" id="locationStatus">We need your exact address for pickup.</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Select Date</label>
                                <input type="date" class="form-control" id="pickupDate" required>
                            </div>
                            
                            <div class="text-end">
                                <button type="button" class="btn text-white px-4 rounded-pill btn-next" style="background-color: var(--primary-green);" data-next="2">Next Step <i class="fa-solid fa-arrow-right ms-2"></i></button>
                            </div>
                        </div>

                        <!-- Step 2: Uploads & Notes -->
                        <div class="booking-step d-none" id="step2">
                            <h5 class="fw-bold mb-4">Scrap Details</h5>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold">Upload Images</label>
                                <div class="p-4 border border-2 border-dashed rounded-3 text-center bg-light" style="cursor: pointer;" onclick="document.getElementById('scrapImages').click()">
                                    <i class="fa-solid fa-cloud-arrow-up fa-3x text-muted mb-2"></i>
                                    <p class="mb-0 text-muted">Click to browse or drag images here</p>
                                    <p class="small text-muted mb-0">(Upload multiple photos of the scrap)</p>
                                </div>
                                <input class="form-control d-none" type="file" id="scrapImages" multiple accept="image/*" onchange="updateImageCount(this)">
                                <div id="imageCountDisplay" class="mt-2 fw-bold d-none" style="color: var(--primary-green);">0 images selected</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Additional Notes (Optional)</label>
                                <textarea class="form-control" id="scrapNotes" rows="3" placeholder="Approximate weight, condition, or special instructions..."></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-light px-4 rounded-pill border btn-prev" data-prev="1"><i class="fa-solid fa-arrow-left me-2"></i> Back</button>
                                <button type="button" class="btn text-white px-4 rounded-pill btn-next" style="background-color: var(--primary-green);" data-next="3">Review <i class="fa-solid fa-arrow-right ms-2"></i></button>
                            </div>
                        </div>

                        <!-- Step 3: Review -->
                        <div class="booking-step d-none" id="step3">
                            <h5 class="fw-bold mb-4">Review Summary</h5>
                            
                            <div class="bg-light p-4 rounded-4 mb-4 border">
                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Category:</div>
                                    <div class="col-sm-8 fw-bold" id="summaryCategory"></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Location:</div>
                                    <div class="col-sm-8 fw-bold" id="summaryLocation"></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Date:</div>
                                    <div class="col-sm-8 fw-bold" id="summaryDate"></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Images:</div>
                                    <div class="col-sm-8 fw-bold" id="summaryImages"></div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 text-muted">Notes:</div>
                                    <div class="col-sm-8" id="summaryNotes" style="font-style: italic;"></div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-light px-4 rounded-pill border btn-prev" data-prev="2"><i class="fa-solid fa-arrow-left me-2"></i> Edit</button>
                                <button type="button" class="btn text-white px-4 rounded-pill" style="background-color: var(--primary-blue);" onclick="submitBooking()"><i class="fa-solid fa-check-circle me-2"></i> Confirm Booking</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const isLoggedIn = localStorage.getItem('auth_token') !== null;
        let currentSubcategoryName = '';
        
        document.querySelectorAll('.subcategory-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const subName = this.getAttribute('data-name');
                currentSubcategoryName = subName;
                
                if(!isLoggedIn) {
                    document.getElementById('loginSubcategoryName').innerText = subName;
                    var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                    loginModal.show();
                } else {
                    window.location.href = '/customer/request-pickup?subcategory=' + this.getAttribute('data-uuid');
                }
            });
        });

        // Multi-step logic
        document.querySelectorAll('.btn-next').forEach(btn => {
            btn.addEventListener('click', function() {
                const nextStep = this.getAttribute('data-next');
                
                // Basic validation for Step 1
                if(nextStep == 2) {
                    if(!document.getElementById('pickupLocation').value || !document.getElementById('pickupDate').value) {
                        alert('Please provide both location and date before proceeding.');
                        return;
                    }
                }
                
                // Populate summary for Step 3
                if(nextStep == 3) {
                    document.getElementById('summaryCategory').innerText = currentSubcategoryName;
                    document.getElementById('summaryLocation').innerText = document.getElementById('pickupLocation').value;
                    document.getElementById('summaryDate').innerText = document.getElementById('pickupDate').value;
                    
                    const files = document.getElementById('scrapImages').files;
                    document.getElementById('summaryImages').innerText = files.length > 0 ? files.length + ' image(s) attached' : 'No images attached';
                    
                    document.getElementById('summaryNotes').innerText = document.getElementById('scrapNotes').value || 'None';
                }

                showStep(nextStep);
            });
        });

        document.querySelectorAll('.btn-prev').forEach(btn => {
            btn.addEventListener('click', function() {
                showStep(this.getAttribute('data-prev'));
            });
        });

        function showStep(stepNum) {
            document.querySelectorAll('.booking-step').forEach(el => el.classList.add('d-none'));
            document.getElementById('step' + stepNum).classList.remove('d-none');
            
            // Update progress bar
            const progress = document.getElementById('bookingProgress');
            progress.style.width = (stepNum * 33.33) + '%';
            
            // Update circles
            const step2El = document.getElementById('stepIndicator2');
            if (stepNum >= 2) {
                step2El.className = 'text-white rounded-circle d-flex align-items-center justify-content-center step-circle';
                step2El.style.backgroundColor = 'var(--primary-green)';
            } else {
                step2El.className = 'bg-light text-muted border rounded-circle d-flex align-items-center justify-content-center step-circle';
                step2El.style.backgroundColor = '';
            }

            const step3El = document.getElementById('stepIndicator3');
            if (stepNum >= 3) {
                step3El.className = 'text-white rounded-circle d-flex align-items-center justify-content-center step-circle';
                step3El.style.backgroundColor = 'var(--primary-green)';
            } else {
                step3El.className = 'bg-light text-muted border rounded-circle d-flex align-items-center justify-content-center step-circle';
                step3El.style.backgroundColor = '';
            }
        }

        function resetBookingForm() {
            document.getElementById('bookingForm').reset();
            document.getElementById('imageCountDisplay').classList.add('d-none');
            showStep(1);
            
            // Auto fill today's date
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('pickupDate').value = today;
        }

        function updateImageCount(input) {
            const display = document.getElementById('imageCountDisplay');
            if(input.files && input.files.length > 0) {
                display.innerText = input.files.length + ' image(s) selected';
                display.classList.remove('d-none');
            } else {
                display.classList.add('d-none');
            }
        }

        function submitBooking() {
            // UI preview finish
            alert('Booking confirmed successfully! (Preview Mode)');
            var bookingModal = bootstrap.Modal.getInstance(document.getElementById('bookingModal'));
            bookingModal.hide();
        }

        // Location Detection in Form
        document.getElementById('btnDetectLocation').addEventListener('click', function() {
            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Locating...';
            
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&zoom=18`)
                        .then(response => response.json())
                        .then(data => {
                            if(data && data.display_name) {
                                document.getElementById('pickupLocation').value = data.display_name;
                                document.getElementById('locationStatus').innerText = 'Location automatically detected.';
                                document.getElementById('locationStatus').className = 'text-success small';
                            }
                            btn.innerHTML = originalText;
                        })
                        .catch(err => {
                            btn.innerHTML = originalText;
                            alert('Failed to get address. Please type it manually.');
                        });
                }, function(error) {
                    btn.innerHTML = originalText;
                    alert('Location access denied. Please type your address manually.');
                });
            } else {
                btn.innerHTML = originalText;
                alert('Geolocation not supported by browser.');
            }
        });
    </script>
@endpush
@endsection

