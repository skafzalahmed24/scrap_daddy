@extends('layouts.app')

@section('title', 'Request Pickup - Scrap Daddy')

@push('styles')
    @include('partials.customer.styles')
    <style>
        .slot-date-btn {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: #fff;
            min-width: 90px;
            flex: 1;
        }
        .slot-date-btn.selected {
            border-color: #0d6efd;
            background: #f0f7ff;
        }
        .slot-date-btn.disabled {
            background: #f5f5f5;
            color: #aaa;
            cursor: not-allowed;
            border-color: #eee;
        }
        .slot-date-title {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 4px;
            display: block;
        }
        .slot-date-btn.selected .slot-date-title {
            color: #0d6efd;
        }
        .slot-date-val {
            font-size: 1.1rem;
            font-weight: bold;
            color: #333;
            display: block;
        }
        .slot-date-btn.selected .slot-date-val {
            color: #0d6efd;
        }
        .time-slot-pill {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 0.85rem;
            text-align: center;
            cursor: pointer;
            background: #fff;
            transition: all 0.2s;
            color: #555;
            display: inline-block;
            width: 100%;
        }
        .time-slot-pill:hover {
            border-color: #ccc;
        }
        .time-slot-pill.selected {
            border-color: #0d6efd;
            background: #f0f7ff;
            color: #0d6efd;
            font-weight: 600;
        }
        .accordion-button:not(.collapsed) {
            background-color: transparent;
            color: inherit;
            box-shadow: none;
        }
        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(0,0,0,.125);
        }
    </style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row g-4">
        
        <!-- Left Column: Form Section -->
        <div class="col-lg-8">
            <!-- Hero Header -->
            <div class="hero-banner shadow-sm mb-0 p-4 p-md-5">
                <div class="hero-banner-bg"></div>
                <div class="hero-banner-content">
                    <h2 class="fw-bold" style="color: #0d2b4d;">Request a Pickup</h2>
                    <p class="text-muted fs-6 mb-0">Book a doorstep pickup in 3 simple steps</p>
                </div>
            </div>

            <!-- Form Container -->
            <div class="form-container p-4 p-md-5">
                
                <!-- Stepper -->
                <div class="stepper-wrapper mb-5 d-none d-md-flex">
                    <div class="stepper-line"></div>
                    
                    <div class="step-item active" id="step-indicator-1">
                        <div class="step-circle"><i class="fa-solid fa-box"></i></div>
                        <div class="step-text">
                            <span class="step-num">1</span>
                            <span class="step-title">Details & Location</span>
                        </div>
                    </div>
                    
                    <div class="step-item" id="step-indicator-2">
                        <div class="step-circle"><i class="fa-regular fa-calendar-check"></i></div>
                        <div class="step-text">
                            <span class="step-num">2</span>
                            <span class="step-title">Schedule Pickup</span>
                        </div>
                    </div>
                    
                    <div class="step-item" id="step-indicator-3">
                        <div class="step-circle"><i class="fa-solid fa-check-double"></i></div>
                        <div class="step-text">
                            <span class="step-num">3</span>
                            <span class="step-title">Review & Confirm</span>
                        </div>
                    </div>
                </div>

                <form id="pickupForm" action="{{ route('customer.orders.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_uuid" value="">
                    <input type="hidden" name="subcategory_uuid" id="input_subcategory_uuid" value="{{ request('subcategory') }}">
                    <input type="hidden" name="pickup_date" id="input_pickup_date">
                    <input type="hidden" name="pickup_time" id="input_pickup_time">
                    <div id="hiddenImagesContainer"></div>

                    <!-- STEP 1: Details & Location -->
                    <div class="step-container active" id="step-1">
                        
                        <!-- Category Selection -->
                        <div class="mb-4">
                            <div class="section-title">
                                <div class="section-icon"><i class="fa-solid fa-house"></i></div>
                                <h5>What are you looking to sell?</h5>
                            </div>
                            <p class="section-subtitle">Select the type of scrap you want us to pick up</p>
                            
                            <div class="category-grid ms-md-5">
                                @php
                                    $displaySubcategories = request('subcategory') 
                                        ? $subcategories->where('uuid', request('subcategory')) 
                                        : $subcategories;
                                    if ($displaySubcategories->isEmpty()) $displaySubcategories = $subcategories;
                                @endphp
                                @foreach($displaySubcategories as $sub)
                                    <div class="category-card {{ request('subcategory') == $sub->uuid ? 'selected' : '' }}" data-uuid="{{ $sub->uuid }}" data-name="{{ $sub->name }}">
                                        <i class="fa-solid fa-circle-check check-icon"></i>
                                        <div class="category-icon-wrapper">
                                            @if($sub->image)
                                                <img src="/{{ $sub->image }}" alt="" style="width: 24px; object-fit: contain;">
                                            @else
                                                <i class="fa-solid fa-recycle"></i>
                                            @endif
                                        </div>
                                        <div class="category-info">
                                            <h6>{{ $sub->name }}</h6>
                                            <p>{{ Str::limit($sub->description ?? 'Scrap items', 20) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Location Selection -->
                        <div class="mb-4 mt-5">
                            <div class="section-title">
                                <div class="section-icon"><i class="fa-solid fa-location-dot"></i></div>
                                <h5>Pickup Address</h5>
                            </div>
                            <p class="section-subtitle">Enter the complete address for pickup</p>
                            
                            <div class="d-flex flex-column flex-md-row gap-2 mt-3 ms-md-5">
                                <div class="position-relative flex-grow-1">
                                    <i class="fa-solid fa-location-dot position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                    <input type="text" name="pickup_location" id="pickup_location" class="form-control ps-5 py-2 w-100 h-100" placeholder="Door No. 123, Main Street, City - 123456" required style="border-radius: 8px; border: 1px solid #ddd;">
                                </div>
                                <button type="button" class="btn py-2 px-3 fw-bold" id="btnDetectLocation" style="border-radius: 8px; white-space: nowrap; background: #e8f5e9; color: var(--primary-green, #1b5e20); border: 1px solid #c8e6c9;">
                                    <i class="fa-solid fa-crosshairs me-2"></i> Use my location
                                </button>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-primary next-step" data-target="2" style="background-color: var(--primary-green, #1b5e20); border-color: var(--primary-green, #1b5e20);">Next Step <i class="fa-solid fa-arrow-right ms-2"></i></button>
                        </div>
                    </div>

                    <!-- STEP 2: Date & Media -->
                    <div class="step-container" id="step-2">
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3" style="color: #444;">Select pickup slots</h6>
                            <div class="border rounded-3 p-3 d-flex justify-content-between align-items-center bg-white cursor-pointer" data-bs-toggle="modal" data-bs-target="#datePickerModal" style="border-color: #e0e0e0 !important; cursor: pointer;">
                                <div class="d-flex align-items-center">
                                    <i class="fa-regular fa-calendar text-muted me-3 fs-5"></i>
                                    <div>
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">Pickup Date</small>
                                        <strong style="color: #222; font-size: 1rem;" id="selectedDateDisplay">Select a date</strong>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right text-muted"></i>
                            </div>
                        </div>

                        <div class="mb-4 mt-4">
                            <div class="accordion accordion-flush" id="timeSlotsAccordion">
                                <!-- Morning -->
                                <div class="accordion-item border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button px-0 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMorning" aria-expanded="true">
                                            <div class="d-flex align-items-center w-100">
                                                <i class="fa-solid fa-cloud-sun text-primary me-3 fs-5"></i>
                                                <div>
                                                    <h6 class="mb-0 fw-bold" style="color: #444;">Morning</h6>
                                                    <small class="text-muted" style="font-size: 0.75rem;">07:00 AM - 12:00 PM</small>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapseMorning" class="accordion-collapse collapse show" data-bs-parent="#timeSlotsAccordion">
                                        <div class="accordion-body px-0 py-3">
                                            <div class="row g-2" id="morningSlots">
                                                <!-- Slots generated by JS -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Afternoon -->
                                <div class="accordion-item border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed px-0 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAfternoon">
                                            <div class="d-flex align-items-center w-100">
                                                <i class="fa-solid fa-sun text-warning me-3 fs-5"></i>
                                                <div>
                                                    <h6 class="mb-0 fw-bold" style="color: #444;">Afternoon</h6>
                                                    <small class="text-muted" style="font-size: 0.75rem;">12:30 PM - 03:30 PM</small>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapseAfternoon" class="accordion-collapse collapse" data-bs-parent="#timeSlotsAccordion">
                                        <div class="accordion-body px-0 py-3">
                                            <div class="row g-2" id="afternoonSlots">
                                                <!-- Slots generated by JS -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Evening -->
                                <div class="accordion-item border-bottom">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed px-0 py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEvening">
                                            <div class="d-flex align-items-center w-100">
                                                <i class="fa-solid fa-cloud-moon text-secondary me-3 fs-5"></i>
                                                <div>
                                                    <h6 class="mb-0 fw-bold" style="color: #444;">Evening</h6>
                                                    <small class="text-muted" style="font-size: 0.75rem;">04:00 PM - 09:30 PM</small>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapseEvening" class="accordion-collapse collapse" data-bs-parent="#timeSlotsAccordion">
                                        <div class="accordion-body px-0 py-3">
                                            <div class="row g-2" id="eveningSlots">
                                                <!-- Slots generated by JS -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 mt-5">
                            <div class="section-title">
                                <div class="section-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                                <h5>Upload Disposal Images & Description</h5>
                            </div>
                            <div class="ms-md-5 mt-3">
                                <div class="row g-2 mb-3">
                                    <div class="col-12 col-md-6">
                                        <label class="custom-file-upload w-100" for="imageUpload">
                                            <div class="upload-btn"><i class="fa-solid fa-cloud-arrow-up me-2"></i> Choose Files</div>
                                        </label>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="custom-file-upload w-100" for="cameraUpload">
                                            <div class="upload-btn"><i class="fa-solid fa-camera me-2"></i> Take Photo</div>
                                        </label>
                                    </div>
                                </div>
                                <span class="file-name-display d-block mb-3" id="fileNameDisplay">No file chosen</span>
                                <input type="file" id="imageUpload" class="d-none" multiple accept="image/*">
                                <input type="file" id="cameraUpload" class="d-none" accept="image/*" capture="environment">
                                <div id="uploadProgress" class="text-primary mb-2" style="display:none; font-size: 0.8rem;">Uploading images...</div>
                                <div id="imagePreviewContainer" class="mb-3"></div>
                                
                                <div class="textarea-wrapper mt-3">
                                    <i class="fa-solid fa-pencil"></i>
                                    <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Any specific instructions for the pickup team?"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-5 pt-3 border-top">
                            <button type="button" class="btn btn-light px-4 py-2 rounded fw-bold border prev-step" data-target="1"><i class="fa-solid fa-arrow-left me-2"></i> Back</button>
                            <button type="button" class="btn btn-primary px-5 py-2 rounded fw-bold shadow-sm" id="previewBtn" style="background-color: var(--primary-green, #1b5e20); border-color: var(--primary-green, #1b5e20);">Proceed to review</button>
                        </div>

                        <div class="security-banner">
                            <div class="security-icon-left"><i class="fa-solid fa-shield-halved"></i></div>
                            <div class="security-text">
                                <h6>Your data is secure with us</h6>
                                <p>We never share your information with third parties.</p>
                            </div>
                            <div class="security-icon-right"><i class="fa-solid fa-lock"></i></div>
                        </div>
                    </div>

                    <!-- STEP 3: Preview -->
                    <div class="step-container" id="step-3">
                        <div class="section-title">
                            <div class="section-icon"><i class="fa-solid fa-file-lines"></i></div>
                            <h5>Review Your Request</h5>
                        </div>
                        <p class="section-subtitle">Please review your details before confirming the pickup.</p>
                        
                        <div class="review-card ms-md-5">
                            <div class="row">
                                <div class="col-md-6 border-end-md">
                                    <div class="review-item">
                                        <div class="review-icon"><i class="fa-solid fa-box-open"></i></div>
                                        <div class="review-text">
                                            <label>Category</label>
                                            <p id="prev-category"></p>
                                        </div>
                                    </div>
                                    <div class="review-item">
                                        <div class="review-icon"><i class="fa-solid fa-location-dot"></i></div>
                                        <div class="review-text">
                                            <label>Address</label>
                                            <p id="prev-address"></p>
                                        </div>
                                    </div>
                                    <div class="review-item border-0 pb-0">
                                        <div class="review-icon"><i class="fa-regular fa-calendar-check"></i></div>
                                        <div class="review-text">
                                            <label>Date & Time</label>
                                            <p id="prev-datetime"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 ps-md-4 mt-4 mt-md-0">
                                    <div class="review-item">
                                        <div class="review-icon"><i class="fa-regular fa-file-lines"></i></div>
                                        <div class="review-text">
                                            <label>Notes</label>
                                            <p id="prev-notes" style="font-weight: 500;"></p>
                                        </div>
                                    </div>
                                    <div class="review-item">
                                        <div class="review-icon"><i class="fa-regular fa-image"></i></div>
                                        <div class="review-text w-100">
                                            <label>Images</label>
                                            <div class="review-images" id="prev-images"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert-success-custom ms-md-5 mb-4" style="background-color: #e8f5e9; border: 1px solid #c8e6c9; color: #2e7d32;">
                            <i class="fa-solid fa-shield-check"></i>
                            <div>Once confirmed, Scrapdaddy will review your request. Your request will be assigned soon. Please wait for a while.</div>
                        </div>

                        <div class="d-flex justify-content-between ms-md-5">
                            <button type="button" class="btn btn-light px-4 rounded-pill fw-bold border prev-step" data-target="2"><i class="fa-solid fa-arrow-left me-2"></i> Edit Details</button>
                            <button type="submit" class="btn btn-next-step px-4 px-md-5 shadow"><i class="fa-regular fa-circle-check me-2"></i> Confirm Pickup Request</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 24px; z-index: 10;">
            <!-- Why choose card -->
            <div class="sidebar-card mb-4 p-4">
                <h5 class="fw-bold mb-4 d-flex align-items-center" style="color: #222;">
                    <i class="fa-solid fa-leaf me-2" style="color: var(--primary-green, #1b5e20);"></i> Why choose ScrapDaddy?
                </h5>
                <ul class="list-unstyled feature-list">
                    <li>
                        <div class="icon"><i class="fa-solid fa-truck-fast"></i></div>
                        <div class="text">
                            <h6>Doorstep Pickup</h6>
                            <p>We pickup at your convenience</p>
                        </div>
                    </li>
                    <li>
                        <div class="icon"><i class="fa-solid fa-tags"></i></div>
                        <div class="text">
                            <h6>Best Market Price</h6>
                            <p>Get the best rates for your scrap</p>
                        </div>
                    </li>
                    <li>
                        <div class="icon"><i class="fa-solid fa-shield-halved"></i></div>
                        <div class="text">
                            <h6>Verified & Trusted</h6>
                            <p>100% verified buyers</p>
                        </div>
                    </li>
                    <li>
                        <div class="icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                        <div class="text">
                            <h6>Instant Updates</h6>
                            <p>Track your request in real-time</p>
                        </div>
                    </li>
                    <li>
                        <div class="icon"><i class="fa-solid fa-seedling"></i></div>
                        <div class="text">
                            <h6>Eco-Friendly</h6>
                            <p>We care for our environment</p>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Need Help card -->
            <div class="sidebar-card p-4">
                <h5 class="fw-bold mb-2 d-flex align-items-center" style="color: #222;">
                    <i class="fa-solid fa-headset me-2" style="color: var(--primary-green, #1b5e20);"></i> Need Help?
                </h5>
                <p class="text-muted small mb-4 ms-4 ps-1">We're here to assist you</p>
                
                <div class="contact-item mb-3">
                    <i class="fa-solid fa-phone" style="color: var(--primary-green, #1b5e20);"></i>
                    <span>1800-SCRAP-NOW</span>
                </div>
                <div class="contact-item">
                    <i class="fa-solid fa-envelope" style="color: var(--primary-green, #1b5e20);"></i>
                    <span>support@scrapdaddy.com</span>
                </div>
            </div>
            </div>
        </div>
    </div>

    <!-- Bottom Trust Badges -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="trust-badges-container">
                <div class="row g-4">
                    <div class="col-6 col-md-3">
                        <div class="trust-item">
                            <i class="fa-solid fa-shield-cat"></i>
                            <div>
                                <h6>Secure & Safe</h6>
                                <p>Your data is protected</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="trust-item">
                            <i class="fa-solid fa-bolt"></i>
                            <div>
                                <h6>Quick & Easy</h6>
                                <p>Book in less than 2 mins</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="trust-item">
                            <i class="fa-solid fa-users"></i>
                            <div>
                                <h6>Trusted by 1000+ Users</h6>
                                <p>Across 50+ Cities</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="trust-item">
                            <i class="fa-solid fa-star"></i>
                            <div>
                                <h6>Rated 4.8/5</h6>
                                <p>By our happy customers</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Location Error Modal -->
<div class="modal fade" id="locationErrorModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-danger">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> Location Error
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center pt-4 pb-4">
        <div class="mb-3">
            <i class="fa-solid fa-map-location-dot fa-3x text-muted opacity-50"></i>
        </div>
        <p id="locationErrorMessage" class="text-secondary fw-medium mb-0"></p>
      </div>
      <div class="modal-footer border-0 pt-0 justify-content-center">
        <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Got it</button>
      </div>
    </div>
  </div>
</div>

<!-- Date Picker Modal -->
<div class="modal fade" id="datePickerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content border-0 shadow" style="border-radius: 16px;">
      <div class="modal-header border-0 pb-0 pt-4 px-4">
        <h4 class="modal-title fw-bold" style="color: #222;">Select date</h4>
      </div>
      <div class="modal-body px-4 py-3" id="dateModalList">
        <!-- Populated by JS -->
      </div>
      <div class="modal-footer border-0 pt-0 pb-4 justify-content-center">
        <button type="button" class="btn btn-link text-danger text-decoration-none fw-bold" data-bs-dismiss="modal"><i class="fa-solid fa-xmark me-2"></i> Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content border-0 text-center p-4 shadow" style="border-radius: 20px;">
      <div class="modal-body p-0">
        <div class="mb-3">
            <div style="width: 80px; height: 80px; background-color: #2e7d32; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; box-shadow: 0 0 0 10px #e8f5e9;">
                <i class="fa-solid fa-check text-white" style="font-size: 2.5rem;"></i>
            </div>
        </div>
        <h4 class="fw-bold mb-4 mt-4" style="color: #222;">Request raised</h4>
        <div class="border rounded-3 p-3 mb-4 d-flex align-items-center justify-content-center text-start mx-auto" style="max-width: 100%; border-color: #eee !important;">
            <i class="fa-regular fa-calendar-days fs-2 text-primary me-3" style="color: #4285f4 !important;"></i>
            <div>
                <small class="text-muted d-block" style="font-size:0.75rem;">Pickup date</small>
                <strong style="color: #222; font-size: 0.95rem;" id="successModalDate">Today, 02 Jul 2026</strong>
            </div>
        </div>
        <p class="text-muted small mb-4">Your request will be assigned soon.<br>Please wait for a while.</p>
        <button type="button" class="btn w-100 py-2 fw-bold shadow-sm" id="btnSuccessOkay" style="background-color: #2e7d32; color: #fff; border-radius: 10px; font-size: 1rem;">Okay</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- Authentication Check ---
        const token = localStorage.getItem('auth_token');
        if (!token) {
            // Redirect to login, preserving query parameters like ?subcategory=...
            const currentUrl = encodeURIComponent(window.location.pathname + window.location.search);
            window.location.href = '/customer/login?redirect=' + currentUrl;
            return;
        }

        // Populate user_uuid from local storage if available
        const userData = localStorage.getItem('user_data');
        if (userData) {
            try {
                const user = JSON.parse(userData);
                if (user && user.uuid) {
                    const uuidInput = document.querySelector('input[name="user_uuid"]');
                    if (uuidInput) uuidInput.value = user.uuid;
                }
            } catch (e) {
                console.error('Failed to parse user data', e);
            }
        }
        
        function showLocationError(msg) {
            document.getElementById('locationErrorMessage').innerText = msg;
            new bootstrap.Modal(document.getElementById('locationErrorModal')).show();
        }

        // Category Card Selection
        document.querySelectorAll('.category-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.category-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                document.getElementById('input_subcategory_uuid').value = this.dataset.uuid;
            });
        });

        // Navigation Logic
        const showStep = (step) => {
            document.querySelectorAll('.step-container').forEach(el => el.classList.remove('active'));
            document.getElementById(`step-${step}`).classList.add('active');
            
            // Update Indicators
            for(let i=1; i<=3; i++) {
                const ind = document.getElementById(`step-indicator-${i}`);
                if(ind) {
                    if(i < step) {
                        ind.classList.add('completed');
                        ind.classList.remove('active');
                    } else if(i == step) {
                        ind.classList.add('active');
                        ind.classList.remove('completed');
                    } else {
                        ind.classList.remove('active', 'completed');
                    }
                }
            }
        };

        document.querySelectorAll('.next-step, .prev-step').forEach(btn => {
            btn.addEventListener('click', function() {
                const target = this.getAttribute('data-target');
                if(this.classList.contains('next-step') && target == 2) {
                    if(!document.getElementById('input_subcategory_uuid').value) {
                        alert("Please select a scrap category."); return;
                    }
                    if(!document.getElementById('pickup_location').value) {
                        alert("Please enter a pickup address."); return;
                    }
                }
                showStep(target);
            });
        });

        // Date Modal Logic
        const dateModalList = document.getElementById('dateModalList');
        const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        const fullDays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        
        let selectedDateDisplayStr = '';
        
        for(let i=0; i<7; i++) {
            const d = new Date(); d.setDate(d.getDate() + i);
            const dateStr = d.toISOString().split('T')[0];
            
            let dayText = fullDays[d.getDay()];
            if (i === 0) dayText = 'Today';
            if (i === 1) dayText = 'Tomorrow';
            let dateFormatted = `${d.getDate().toString().padStart(2, '0')} ${months[d.getMonth()]} ${d.getFullYear()}`;
            
            const label = document.createElement('label');
            label.className = 'd-flex align-items-center w-100 p-3 mb-2 border rounded-3 cursor-pointer date-list-item';
            label.style.borderColor = '#eee';
            
            // pre-select tomorrow
            const isChecked = (i === 1) ? 'checked' : '';
            if(i === 1) {
                document.getElementById('input_pickup_date').value = dateStr;
                selectedDateDisplayStr = `Tomorrow, ${dateFormatted}`;
                document.getElementById('selectedDateDisplay').innerText = selectedDateDisplayStr;
                label.style.borderColor = '#2e7d32';
                label.style.backgroundColor = '#f1f8f1';
            }

            let titleClass = (i === 0 || i === 1) ? 'fw-bolder' : 'fw-bold text-dark';
            let titleStyle = (i === 0 || i === 1) ? 'color: #1b5e20; font-size:1rem;' : 'font-size:0.95rem;';
            let suggestionBadge = (i === 0 || i === 1) ? '<span class="badge ms-2 rounded-pill" style="background: rgba(27,94,32,0.1); color: #1b5e20; font-size: 0.65rem; border: 1px solid rgba(27,94,32,0.2);">Suggested</span>' : '';

            label.innerHTML = `
                <div class="form-check m-0 d-flex align-items-center w-100">
                    <input class="form-check-input me-3" type="radio" name="date_radio" value="${dateStr}" data-display="${i === 0 ? 'Today' : (i===1 ? 'Tomorrow' : dayText)}, ${dateFormatted}" style="width: 20px; height: 20px;" ${isChecked}>
                    <div>
                        <div class="${titleClass}" style="${titleStyle}">${dayText}${suggestionBadge}</div>
                        <div class="text-muted" style="font-size:0.8rem;">${dateFormatted}</div>
                    </div>
                </div>
            `;
            dateModalList.appendChild(label);
        }

        // Add Custom Date option
        const customLabel = document.createElement('label');
        customLabel.className = 'd-flex flex-column w-100 p-3 mb-2 border rounded-3 cursor-pointer date-list-item';
        customLabel.style.borderColor = '#eee';
        customLabel.innerHTML = `
            <div class="form-check m-0 d-flex align-items-center w-100">
                <input class="form-check-input me-3" type="radio" name="date_radio" value="custom" id="customDateRadio" style="width: 20px; height: 20px;">
                <div class="w-100 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold text-dark" style="font-size:0.95rem;">Custom Date</div>
                        <div class="text-muted" style="font-size:0.8rem;">Select a specific date</div>
                    </div>
                </div>
            </div>
            <div id="customDateContainer" style="display:none; margin-top: 15px; padding-left: 35px;">
                <input type="date" id="customDateModalInput" class="form-control" min="${new Date().toISOString().split('T')[0]}">
            </div>
        `;
        dateModalList.appendChild(customLabel);

        document.getElementById('customDateModalInput').addEventListener('change', function() {
            if(this.value) {
                document.querySelectorAll('.date-list-item').forEach(el => {
                    el.style.borderColor = '#eee';
                    el.style.backgroundColor = 'transparent';
                });
                
                customLabel.style.borderColor = '#2e7d32';
                customLabel.style.backgroundColor = '#f1f8f1';
                customLabel.querySelector('input[type="radio"]').checked = true;
                
                document.getElementById('input_pickup_date').value = this.value;
                
                const selD = new Date(this.value);
                let dateFormatted = `${selD.getDate().toString().padStart(2, '0')} ${months[selD.getMonth()]} ${selD.getFullYear()}`;
                
                selectedDateDisplayStr = `Custom, ${dateFormatted}`;
                document.getElementById('selectedDateDisplay').innerText = selectedDateDisplayStr;
                
                // close modal
                setTimeout(() => {
                    bootstrap.Modal.getInstance(document.getElementById('datePickerModal')).hide();
                }, 300);
            }
        });

        // Handle date selection in modal
        dateModalList.addEventListener('change', function(e) {
            if(e.target.name === 'date_radio') {
                if(e.target.value === 'custom') {
                    document.getElementById('customDateContainer').style.display = 'block';
                    try { document.getElementById('customDateModalInput').showPicker(); } catch(err) {}
                    return;
                } else {
                    document.getElementById('customDateContainer').style.display = 'none';
                }
                document.querySelectorAll('.date-list-item').forEach(el => {
                    el.style.borderColor = '#eee';
                    el.style.backgroundColor = 'transparent';
                });
                
                const selectedLabel = e.target.closest('.date-list-item');
                selectedLabel.style.borderColor = '#2e7d32';
                selectedLabel.style.backgroundColor = '#f1f8f1';
                
                document.getElementById('input_pickup_date').value = e.target.value;
                selectedDateDisplayStr = e.target.dataset.display;
                document.getElementById('selectedDateDisplay').innerText = selectedDateDisplayStr;
                
                // close modal
                setTimeout(() => {
                    bootstrap.Modal.getInstance(document.getElementById('datePickerModal')).hide();
                }, 300);
            }
        });

        // Time Slot Generator
        function generateSlots(containerId, startHour, endHour, ampm) {
            const container = document.getElementById(containerId);
            container.innerHTML = '';
            for(let h=startHour; h<=endHour; h++) {
                let hour12 = h > 12 ? h - 12 : h;
                if (hour12 === 0) hour12 = 12;
                
                // XX:00 slot
                let slot1 = document.createElement('div');
                slot1.className = 'col-4 col-sm-3 col-md-3';
                let timeStr1 = `${hour12}:00 ${ampm}`;
                slot1.innerHTML = `<div class="time-slot-pill" data-time="${timeStr1}">${timeStr1}</div>`;
                container.appendChild(slot1);
                
                // XX:30 slot (skip if it's the end of range and we want to stop exactly at endHour:00)
                if(h < endHour || (containerId === 'afternoonSlots' && h === endHour)) {
                    let slot2 = document.createElement('div');
                    slot2.className = 'col-4 col-sm-3 col-md-3';
                    let timeStr2 = `${hour12}:30 ${ampm}`;
                    slot2.innerHTML = `<div class="time-slot-pill" data-time="${timeStr2}">${timeStr2}</div>`;
                    container.appendChild(slot2);
                }
            }
        }
        
        generateSlots('morningSlots', 7, 11, 'AM');
        
        // Custom generation for afternoon (12:30 PM - 3:30 PM)
        const afternoonContainer = document.getElementById('afternoonSlots');
        afternoonContainer.innerHTML = '';
        ['12:30 PM', '1:00 PM', '1:30 PM', '2:00 PM', '2:30 PM', '3:00 PM', '3:30 PM'].forEach(time => {
            let slot = document.createElement('div');
            slot.className = 'col-4 col-sm-3 col-md-3';
            slot.innerHTML = `<div class="time-slot-pill" data-time="${time}">${time}</div>`;
            afternoonContainer.appendChild(slot);
        });

        // Evening (4:00 PM - 9:30 PM)
        const eveningContainer = document.getElementById('eveningSlots');
        eveningContainer.innerHTML = '';
        ['4:00 PM', '4:30 PM', '5:00 PM', '5:30 PM', '6:00 PM', '6:30 PM', '7:00 PM', '7:30 PM', '8:00 PM', '8:30 PM', '9:00 PM', '9:30 PM'].forEach(time => {
            let slot = document.createElement('div');
            slot.className = 'col-4 col-sm-3 col-md-3';
            slot.innerHTML = `<div class="time-slot-pill" data-time="${time}">${time}</div>`;
            eveningContainer.appendChild(slot);
        });

        // Time Selector
        document.getElementById('timeSlotsAccordion').addEventListener('click', function(e) {
            const pill = e.target.closest('.time-slot-pill');
            if (pill) {
                document.querySelectorAll('.time-slot-pill').forEach(b => b.classList.remove('selected'));
                pill.classList.add('selected');
                document.getElementById('input_pickup_time').value = pill.dataset.time;
            }
        });

        // Location Detection
        document.getElementById('btnDetectLocation').addEventListener('click', function() {
            const btn = this; const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Locating...';
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${position.coords.latitude}&lon=${position.coords.longitude}&zoom=18`, {
                        headers: { 'Accept-Language': 'en-US,en;q=0.9' }
                    })
                        .then(r => r.json()).then(data => {
                            if(data && data.display_name) document.getElementById('pickup_location').value = data.display_name;
                            btn.innerHTML = originalText;
                        }).catch(() => { btn.innerHTML = originalText; showLocationError('Failed to get address from coordinates.'); });
                }, (error) => { 
                    btn.innerHTML = originalText; 
                    let msg = 'Location access denied.';
                    if (error.code === 1) msg = 'Please allow location access in your browser settings (click the lock icon in the address bar).';
                    else if (error.code === 2) msg = 'Location information is unavailable right now.';
                    else if (error.code === 3) msg = 'The request to get your location timed out.';
                    
                    if (window.isSecureContext === false) {
                        msg = 'Auto-location requires a secure connection (HTTPS) or localhost. Please type your address manually.';
                    }
                    showLocationError(msg);
                }, { enableHighAccuracy: true, timeout: 10000 });
            } else { btn.innerHTML = originalText; showLocationError('Geolocation not supported by your browser.'); }
        });

        // Image Upload via AJAX
        const handleImageUpload = async function(files) {
            const display = document.getElementById('fileNameDisplay');
            if(files.length === 0) {
                if(display) display.innerText = "No file chosen";
                return;
            }
            if(display) display.innerText = files.length + " file(s) selected";
            
            const formData = new FormData();
            for(let i=0; i<files.length; i++) formData.append('images[]', files[i]);
            document.getElementById('uploadProgress').style.display = 'block';
            try {
                const res = await fetch('/api/upload-images', { method: 'POST', body: formData, headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                if(data.success) {
                    data.paths.forEach(path => {
                        const wrapper = document.createElement('div'); wrapper.className = 'image-preview-wrapper';
                        wrapper.innerHTML = `<img src="/${path}"><button type="button" class="remove-img-btn" onclick="this.parentElement.remove(); document.querySelector('input[value=\\'${path}\\']').remove();">&times;</button>`;
                        document.getElementById('imagePreviewContainer').appendChild(wrapper);
                        const hiddenInput = document.createElement('input'); hiddenInput.type = 'hidden'; hiddenInput.name = 'images[]'; hiddenInput.value = path;
                        document.getElementById('hiddenImagesContainer').appendChild(hiddenInput);
                    });
                } else alert("Upload failed");
            } catch(e) { alert("Upload error."); } 
            finally { document.getElementById('uploadProgress').style.display = 'none'; }
        };

        const imageUpload = document.getElementById('imageUpload');
        if (imageUpload) {
            imageUpload.addEventListener('change', function() { handleImageUpload(this.files); this.value = ''; });
        }
        
        const cameraUpload = document.getElementById('cameraUpload');
        if (cameraUpload) {
            cameraUpload.addEventListener('change', function() { handleImageUpload(this.files); this.value = ''; });
        }

        // Preview Logic
        document.getElementById('previewBtn').addEventListener('click', function() {
            if(!document.getElementById('input_pickup_date').value || !document.getElementById('input_pickup_time').value) {
                alert("Please select a date and time slot."); return;
            }
            const selCard = document.querySelector('.category-card.selected');
            document.getElementById('prev-category').innerText = selCard ? selCard.dataset.name : 'Not selected';
            document.getElementById('prev-address').innerText = document.getElementById('pickup_location').value;
            
            // Format Date & Time
            const dateStr = document.getElementById('input_pickup_date').value;
            const timeStr = document.getElementById('input_pickup_time').value;
            const dateObj = new Date(dateStr);
            const formattedDate = dateObj.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
            const dayName = dateObj.toLocaleDateString('en-GB', { weekday: 'short' });
            document.getElementById('prev-datetime').innerText = `${formattedDate} (${dayName}) • ${timeStr}`;

            document.getElementById('prev-notes').innerText = document.getElementById('notes').value || 'None';
            
            const prevImages = document.getElementById('prev-images'); prevImages.innerHTML = '';
            document.querySelectorAll('#hiddenImagesContainer input').forEach(inp => {
                prevImages.innerHTML += `<img src="/${inp.value}">`;
            });
            showStep(3);
        });

        // Form Submit Logic for Success Popup
        const pickupForm = document.getElementById('pickupForm');
        if(pickupForm) {
            pickupForm.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent direct submission
                
                // Basic validation check
                if(!document.getElementById('input_pickup_date').value || !document.getElementById('input_pickup_time').value) {
                    alert("Please select a date and time slot."); return;
                }
                
                // Set date on success modal
                const successDateEl = document.getElementById('successModalDate');
                if(successDateEl) successDateEl.innerText = selectedDateDisplayStr || document.getElementById('input_pickup_date').value;
                
                // Show Success Modal
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            });
        }

        // Okay button logic
        const btnSuccessOkay = document.getElementById('btnSuccessOkay');
        if(btnSuccessOkay) {
            btnSuccessOkay.addEventListener('click', function() {
                document.getElementById('pickupForm').submit();
            });
        }

    });
</script>
@endpush
