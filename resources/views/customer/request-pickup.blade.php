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
        /* Step navigation buttons: one clean row, equal height, never stacked icon/text */
        .step-nav { display: flex; gap: 12px; align-items: stretch; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #eee; }
        .step-nav .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; white-space: nowrap; min-height: 48px; padding: 0 22px; border-radius: 12px; font-weight: 700; font-size: .95rem; line-height: 1.2; }
        .step-nav .btn-back { flex: 0 0 auto; background: #fff; border: 1px solid #d6d6d6; color: #333; }
        .step-nav .btn-back:hover { background: #f5f5f5; }
        .step-nav .btn-primary-action { flex: 1 1 auto; background: var(--primary-green, #3D8B4D); border: 1px solid var(--primary-green, #3D8B4D); color: #fff; box-shadow: 0 4px 12px rgba(61,139,77,.25); }
        .step-nav .btn-primary-action:hover { filter: brightness(1.08); color: #fff; }
        .step-nav .btn i { margin: 0 !important; }
        .qty-display { min-width: 28px; text-align: center; }
        /* ===== App-style look for steps 1 & 2 (mirrors the Android screens) ===== */
        .cat-hero { display:flex; align-items:center; justify-content:space-between; gap:16px; margin:6px 0 18px; }
        .cat-hero h3 { font-size:1.9rem; font-weight:800; color:#14201a; line-height:1.15; margin:0 0 6px; letter-spacing:-.02em; }
        .cat-hero p { color:#5f6b66; margin:0; font-size:.95rem; max-width:340px; }
        .cat-hero img { width:120px; height:120px; object-fit:contain; flex-shrink:0; }
        @media (max-width:575px){ .cat-hero h3{font-size:1.6rem} .cat-hero img{width:96px;height:96px} }
        #step-1 .item-row { display:flex; border-radius:20px !important; border:1px solid rgba(16,24,40,.06) !important; box-shadow:0 6px 20px rgba(16,24,40,.07); padding:14px !important; }
        #step-1 .item-icon-wrapper { background:#f1f8f1 !important; border:1.5px solid #cfe8d2; border-radius:16px !important; width:64px !important; height:64px !important; }
        #step-1 .item-info h6 { font-size:1.05rem; }
        #step-1 .quantity-controls .btn-minus, #step-1 .quantity-controls .btn-plus { width:36px !important; height:36px !important; border:0 !important; color:#fff !important; background:linear-gradient(135deg,#4A9A58,#3D8B4D) !important; box-shadow:0 3px 8px rgba(61,139,77,.3); }
        #step-1 .quantity-controls .qty-display { font-size:1.1rem; }
        .sd-label { display:flex; align-items:center; gap:10px; font-weight:800; font-size:1.05rem; color:#14201a; margin:26px 0 12px; }
        .sd-label i { color:#4A9A58; font-size:1.05rem; }
        .sd-card { background:#fff; border:1.5px solid #e4e7ec; border-radius:18px; padding:18px; }
        .sd-card-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
        .sd-card-head h6 { margin:0; font-weight:800; font-size:1.15rem; }
        .sd-count { background:#e8f5e9; color:#3D8B4D; font-weight:700; font-size:.8rem; padding:5px 12px; border-radius:999px; }
        .sd-pick { display:flex; align-items:center; gap:14px; margin-top:12px; }
        .sd-pick .ph { width:52px; height:52px; border:1.5px solid #e4e7ec; border-radius:14px; display:flex; align-items:center; justify-content:center; background:#fff; flex-shrink:0; }
        .sd-pick .ph img { max-width:100%; max-height:100%; object-fit:contain; }
        .sd-pick b { font-size:1.02rem; color:#14201a; display:block; }
        .sd-pick span { color:#667085; font-size:.9rem; }
        .sd-field { border:1.5px solid #e4e7ec; border-radius:16px; padding:15px 18px; background:#fff; font-weight:700; font-size:1.05rem; color:#14201a; margin-bottom:12px; }
        .sd-field.sm { font-weight:500; font-size:.98rem; }
        .sd-date { display:flex; align-items:center; justify-content:space-between; cursor:pointer; }
        .sd-date i { color:#667085; }
        .sd-periods { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:12px; }
        .period-pill { border:1.5px solid #e4e7ec; background:#fff; border-radius:14px; padding:12px 6px; font-weight:600; color:#667085; display:flex; align-items:center; justify-content:center; gap:8px; cursor:pointer; font-size:.95rem; }
        .period-pill.active { border-color:#66bb6a; background:#eaf6ea; color:#3D8B4D; font-weight:700; }
        #timeSlotsAccordion .accordion-header { display:none; }
        #timeSlotsAccordion .accordion-item { border:0 !important; background:transparent; }
        #timeSlotsAccordion .accordion-body { padding:0 !important; }
        #timeSlotsAccordion .row > div { flex:0 0 auto; width:33.3333%; }
        #timeSlotsAccordion .time-slot-pill { border:1.5px solid #e4e7ec; border-radius:14px; padding:12px 4px; font-size:.9rem; color:#667085; background:#fff; white-space:nowrap; }
        #timeSlotsAccordion .time-slot-pill.selected { border-color:#4A9A58; background:#eaf6ea; color:#3D8B4D; font-weight:700; }
        .sd-upload { display:flex; flex-direction:column; align-items:center; gap:6px; text-align:center; padding:30px 16px; border:1.5px dashed #a5d6a7; background:#f6fbf6; border-radius:20px; cursor:pointer; width:100%; }
        .sd-upload .ic { width:64px; height:64px; border-radius:50%; background:#e3f3e4; color:#4A9A58; display:flex; align-items:center; justify-content:center; font-size:1.5rem; margin-bottom:6px; }
        .sd-upload b { font-size:1.1rem; color:#14201a; }
        .sd-upload small { color:#667085; }
        #step-2 textarea#notes { border:1.5px solid #e4e7ec; border-radius:16px; padding:14px 16px; min-height:120px; }
        #step-2 .step-nav .btn-primary-action { border-radius:999px; min-height:54px; font-size:1.05rem; }
        .item-row .quantity-controls { flex-shrink: 0; }
        .item-row .item-info { min-width: 0; }
        @media (min-width: 768px) { .step-nav .btn-primary-action { flex: 0 1 auto; min-width: 240px; margin-left: auto; } }
        @media (max-width: 380px) { .step-nav .btn { padding: 0 14px; font-size: .88rem; } .step-nav { gap: 8px; } }
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
                    <!-- Cart inputs will be generated dynamically -->
                    <div id="cartHiddenInputs"></div>
                    <input type="hidden" name="pickup_date" id="input_pickup_date">
                    <input type="hidden" name="pickup_time" id="input_pickup_time">
                    <div id="hiddenImagesContainer"></div>

                    <!-- STEP 1: Details & Location -->
                    <div class="step-container active" id="step-1">
                        
                        <!-- Item Selection -->
                        <div class="mb-4">
                            <div class="cat-hero" id="catHero">
                                <div>
                                    <h3 id="catHeroTitle">What are you looking to sell?</h3>
                                    <p id="catHeroSub">Select the items and quantities you want us to pick up</p>
                                </div>
                                <img id="catHeroImg" src="" alt="" style="display:none;">
                            </div>
                            
                            <!-- Search & Filter -->
                            <div class="search-filter-container ms-md-5 mt-3 mb-3">
                                <div class="position-relative mb-3">
                                    <i class="fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                    <input type="text" id="itemSearch" class="form-control ps-5 py-2" placeholder="Search items..." style="border-radius: 20px; border: 1px solid #ddd;">
                                    <button type="button" id="sortToggle" class="btn p-0 border-0 position-absolute top-50 end-0 translate-middle-y me-3 text-success" title="Sort A to Z" aria-label="Sort items"><i class="fa-solid fa-sliders"></i></button>
                                </div>
                                <style>
                                    .cat-chip{background:#e8f5e9!important;color:#3D8B4D!important;border:1.5px solid #a5d6a7!important;font-weight:600;white-space:nowrap}
                                    .cat-chip.active{background:#4A9A58!important;color:#fff!important;border-color:#4A9A58!important;box-shadow:0 4px 12px rgba(74,154,88,.28)}
                                </style>
                                <div class="filter-pills d-flex overflow-auto pb-2" style="white-space: nowrap; scrollbar-width: none;">
                                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 me-2 filter-btn cat-chip active" data-filter="All" data-title="What are you looking to sell?" data-sub="Select the items and quantities you want us to pick up" data-img="">All</button>
                                    @php
                                        // Filter chips = the real categories that have at least one active item
                                        $filterCats = $subcategories->pluck('category')->filter()->unique('uuid')->values();
                                    @endphp
                                    @foreach($filterCats as $cat)
                                        <button type="button" class="btn btn-sm rounded-pill px-3 py-1 me-2 filter-btn cat-chip" data-filter="{{ $cat->uuid }}" data-title="{{ $cat->title }}" data-sub="{{ \Illuminate\Support\Str::limit($cat->description ?: 'Best market prices for your scrap', 70) }}" data-img="{{ $cat->image ? asset($cat->image) : '' }}">{{ $cat->title }}</button>
                                    @endforeach
                                </div>
                            </div>

                            <div id="selectedOnlyBar" class="ms-md-5 mt-3 mb-3 align-items-center justify-content-between gap-2" style="display: none;">
                                <span class="small text-muted">Showing your selected item</span>
                                <button type="button" id="btnAddMoreItems" class="btn btn-sm fw-bold" style="border: 1px solid var(--primary-green, #3D8B4D); color: var(--primary-green, #3D8B4D); border-radius: 20px; white-space: nowrap;"><i class="fa-solid fa-plus me-1"></i> Add more items</button>
                            </div>

                            <div class="items-list ms-md-5" id="itemsList">
                                @foreach($subcategories as $sub)
                                    <div class="item-card bg-white border rounded-3 p-2 mb-3 align-items-center justify-content-between item-row" data-uuid="{{ $sub->uuid }}" data-name="{{ $sub->name }}" data-category="{{ $sub->category_id }}">
                                        <div class="d-flex align-items-center">
                                            <div class="item-icon-wrapper rounded p-1 me-3" style="background: #f1f8f1; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                                @if($sub->image)
                                                    <img src="{{ asset($sub->image) }}" alt="{{ $sub->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                @else
                                                    <i class="fa-solid fa-couch text-success fs-4"></i>
                                                @endif
                                            </div>
                                            <div class="item-info">
                                                <h6 class="mb-0 fw-bold text-dark">{{ $sub->name }}</h6>
                                            </div>
                                        </div>
                                        <div class="quantity-controls d-flex align-items-center">
                                            <button type="button" class="btn btn-sm btn-light rounded-circle border text-success btn-minus" style="width: 32px; height: 32px; padding: 0; line-height: 1;"><i class="fa-solid fa-minus"></i></button>
                                            <span class="mx-3 fw-bold qty-display">0</span>
                                            <button type="button" class="btn btn-sm rounded-circle text-white btn-plus" style="background: var(--primary-green, #3D8B4D); width: 32px; height: 32px; padding: 0; line-height: 1;"><i class="fa-solid fa-plus"></i></button>
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
                                <button type="button" class="btn py-2 px-3 fw-bold" id="btnDetectLocation" style="border-radius: 8px; white-space: nowrap; background: #e8f5e9; color: var(--primary-green, #3D8B4D); border: 1px solid #c8e6c9;">
                                    <i class="fa-solid fa-crosshairs me-2"></i> Use my location
                                </button>
                            </div>
                            <div class="ms-md-5 mt-3">
                                <label for="pin_code" class="form-label fw-semibold small mb-1">PIN code <span class="text-danger">*</span></label>
                                <input type="text" name="pin_code" id="pin_code" inputmode="numeric" maxlength="6" pattern="[1-9][0-9]{5}" class="form-control py-2 w-100" style="border-radius: 8px; border: 1px solid #ddd;" placeholder="6-digit PIN code" value="{{ preg_match('/^[1-9][0-9]{5}$/', (string) ($user->pin_code ?? '')) ? $user->pin_code : '' }}" required>
                                <div id="pinStatus" class="small mt-2" aria-live="polite"></div>
                            </div>
                        </div>

                        <div class="step-nav">
                            <button type="button" class="btn btn-primary-action next-step" data-target="2">Next Step <i class="fa-solid fa-arrow-right"></i></button>
                        </div>
                    </div>

                    <!-- STEP 2: Schedule a Pickup (app layout) -->
                    <div class="step-container" id="step-2">
                        <div class="sd-card">
                            <div class="sd-card-head"><h6>Your Pickup Items</h6><span class="sd-count" id="sumCount">0 Items</span></div>
                            <div id="sumItems"></div>
                        </div>

                        <div class="sd-label"><i class="fa-solid fa-location-dot"></i> Selected Location</div>
                        <div class="sd-field" id="sumLocation">-</div>
                        <div class="sd-field sm" id="sumPin">-</div>

                        <div class="sd-label"><i class="fa-regular fa-calendar"></i> Select Date</div>
                        <div class="sd-field sd-date" data-bs-toggle="modal" data-bs-target="#datePickerModal">
                            <span id="selectedDateDisplay">Select a date</span>
                            <i class="fa-solid fa-caret-down"></i>
                        </div>

                        <div class="sd-label"><i class="fa-regular fa-clock"></i> Time Slot</div>
                        <div class="sd-periods" id="periodPills">
                            <div class="period-pill active" data-period="Morning"><i class="fa-regular fa-sun"></i> Morning</div>
                            <div class="period-pill" data-period="Afternoon"><i class="fa-solid fa-cloud"></i> Afternoon</div>
                            <div class="period-pill" data-period="Evening"><i class="fa-regular fa-moon"></i> Evening</div>
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

                        <div class="sd-label"><i class="fa-solid fa-camera"></i> Add Pictures</div>
                        <label class="sd-upload" for="imageUpload">
                            <span class="ic"><i class="fa-solid fa-camera"></i></span>
                            <b>Tap to add photos</b>
                            <small>JPG, PNG &bull; Max 10 photos &bull; 2 MB each</small>
                        </label>
                        <div class="text-center mt-2"><label for="cameraUpload" class="small fw-semibold" style="color:#4A9A58;cursor:pointer;"><i class="fa-solid fa-camera me-1"></i> or take a photo</label></div>
                        <span class="file-name-display d-block mt-2" id="fileNameDisplay">No file chosen</span>
                        <input type="file" id="imageUpload" class="d-none" multiple accept="image/jpeg,image/png,image/gif,image/webp">
                        <input type="file" id="cameraUpload" class="d-none" accept="image/jpeg,image/png,image/gif,image/webp" capture="environment">
                        <div id="uploadProgress" class="text-primary mb-2" style="display:none; font-size: 0.8rem;">Uploading images...</div>
                        <div id="imagePreviewContainer" class="mt-3"></div>

                        <div class="sd-label"><i class="fa-regular fa-file-lines"></i> Description</div>
                        <textarea name="notes" id="notes" class="form-control" rows="4" placeholder="Enter Description"></textarea>
                        <div class="step-nav">
                            <button type="button" class="btn btn-back prev-step" data-target="1"><i class="fa-solid fa-arrow-left"></i> Back</button>
                            <button type="button" class="btn btn-primary-action" id="previewBtn">Continue <i class="fa-solid fa-arrow-right"></i></button>
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
                                        <div class="review-text w-100">
                                            <label class="mb-2 d-flex justify-content-between align-items-center">
                                                Your Pickup Items 
                                                <span class="badge rounded-pill bg-success-subtle text-success" id="prev-total-items">0 Items</span>
                                            </label>
                                            <div id="prev-items-list" class="border rounded-3 p-2 bg-white">
                                                <!-- Items will be injected here -->
                                            </div>
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

                        <div id="rewardsContainer" style="display: none;">
                            <div class="rewards-card ms-md-5 mb-4 p-3 border rounded-3" style="background-color: #fcfcfc;">
                                <div>
                                    <h6 class="mb-1 fw-bold" style="color: #222;"><i class="fa-solid fa-gift text-success me-2"></i>Rewards Available</h6>
                                    <p class="text-muted small mb-0">You have <strong id="rewardCoinsDisplay">0</strong> coins worth <strong class="text-success" id="rewardBonusDisplay">₹0.00</strong>. You can apply them at payment, once the final amount is set after pickup.</p>
                                </div>
                            </div>
                        </div>

                        <div class="alert-success-custom ms-md-5 mb-4" style="background-color: #e8f5e9; border: 1px solid #c8e6c9; color: #4A9A58;">
                            <i class="fa-solid fa-shield-check"></i>
                            <div>Once confirmed, Scrapdaddy will review your request. Your request will be assigned soon. Please wait for a while.</div>
                        </div>

                        <div class="step-nav">
                            <button type="button" class="btn btn-back prev-step" data-target="2"><i class="fa-solid fa-arrow-left"></i> Edit</button>
                            <button type="submit" class="btn btn-primary-action"><i class="fa-regular fa-circle-check"></i> Confirm Pickup</button>
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
                    <i class="fa-solid fa-leaf me-2" style="color: var(--primary-green, #3D8B4D);"></i> Why choose ScrapDaddy?
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
                    <i class="fa-solid fa-headset me-2" style="color: var(--primary-green, #3D8B4D);"></i> Need Help?
                </h5>
                <p class="text-muted small mb-4 ms-4 ps-1">We're here to assist you</p>
                
                <div class="contact-item mb-3">
                    <i class="fa-solid fa-phone" style="color: var(--primary-green, #3D8B4D);"></i>
                    <span>{{ $site['toll_free'] ?: $site['phone'] }}</span>
                </div>
                <div class="contact-item">
                    <i class="fa-solid fa-envelope" style="color: var(--primary-green, #3D8B4D);"></i>
                    <span>{{ $site['email'] }}</span>
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

<!-- Service not available: contact form -->
<div class="modal fade" id="serviceRequestModal" tabindex="-1" aria-labelledby="serviceRequestTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <form class="modal-content border-0 shadow-lg" id="serviceRequestForm" style="border-radius: 18px;" novalidate>
      <div class="modal-header border-0 pb-0 px-4 pt-4">
        <div>
            <h5 class="modal-title fw-bold" id="serviceRequestTitle" style="color: #222;">We're not in your area yet</h5>
            <p class="text-muted small mb-0 mt-1">Leave your details and we'll contact you as soon as pickup starts in <strong id="srPinLabel">your PIN code</strong>.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body px-4 pt-3">
        <div class="mb-3">
            <label for="srName" class="form-label fw-semibold small mb-1">Full name <span class="text-danger">*</span></label>
            <input type="text" id="srName" class="form-control" maxlength="120" value="{{ $user->full_name ?? '' }}" autocomplete="name" required>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-6">
                <label for="srPhone" class="form-label fw-semibold small mb-1">Phone number <span class="text-danger">*</span></label>
                <input type="tel" id="srPhone" class="form-control" maxlength="15" inputmode="tel" value="{{ $user->phone_number ?? '' }}" autocomplete="tel" required>
            </div>
            <div class="col-12 col-sm-6">
                <label for="srPin" class="form-label fw-semibold small mb-1">PIN code <span class="text-danger">*</span></label>
                <input type="text" id="srPin" class="form-control" maxlength="6" inputmode="numeric" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="srEmail" class="form-label fw-semibold small mb-1">Email <span class="text-muted fw-normal">(optional)</span></label>
            <input type="email" id="srEmail" class="form-control" maxlength="150" autocomplete="email">
        </div>
        <div class="mb-3">
            <label for="srArea" class="form-label fw-semibold small mb-1">Area / locality <span class="text-muted fw-normal">(optional)</span></label>
            <input type="text" id="srArea" class="form-control" maxlength="150" placeholder="e.g. HSR Layout">
        </div>
        <div class="mb-1">
            <label for="srMessage" class="form-label fw-semibold small mb-1">Message <span class="text-muted fw-normal">(optional)</span></label>
            <textarea id="srMessage" class="form-control" rows="3" maxlength="1000" placeholder="What would you like to sell?"></textarea>
        </div>
        <div id="srError" class="text-danger small mt-2" style="display:none;" role="alert"></div>
      </div>
      <div class="modal-footer border-0 px-4 pb-4 pt-0">
        <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Cancel</button>
        <button type="submit" id="srSubmit" class="btn fw-bold px-4 text-white" style="background: var(--primary-green, #3D8B4D); border-radius: 10px;">Submit request</button>
      </div>
    </form>
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
            <div style="width: 80px; height: 80px; background-color: #4A9A58; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; box-shadow: 0 0 0 10px #e8f5e9;">
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
        <button type="button" class="btn w-100 py-2 fw-bold shadow-sm" id="btnSuccessOkay" style="background-color: #4A9A58; color: #fff; border-radius: 10px; font-size: 1rem;">Okay</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
@include('partials.toast')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- Authentication Check ---
        const token = localStorage.getItem('auth_token');
        if (!token) {
            // Redirect to login, preserving query parameters like ?subcategory=...
            const currentUrl = encodeURIComponent(window.location.pathname + window.location.search);
            window.location.href = "{{ url('/customer/login') }}?redirect=" + currentUrl;
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
        
        // Fetch fresh user profile to get latest reward coins balance
        if (token) {
            fetch("{{ url('/api/customer/user') }}", {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 1 && data.data && data.data.user) {
                    const u = data.data.user;
                    if (u.reward_coins > 0) {
                        const coinValue = {{ $coinValue ?? 0.10 }};
                        document.getElementById('rewardCoinsDisplay').innerText = u.reward_coins;
                        document.getElementById('rewardBonusDisplay').innerText = '₹' + (u.reward_coins * coinValue).toFixed(2);
                        document.getElementById('rewardsContainer').style.display = 'block';
                    }
                    // Update local storage so other pages have fresh data
                    localStorage.setItem('user_data', JSON.stringify(u));
                }
            })
            .catch(e => console.error('Failed to fetch user profile', e));
        }
        
        function showLocationError(msg) {
            document.getElementById('locationErrorMessage').innerText = msg;
            new bootstrap.Modal(document.getElementById('locationErrorModal')).show();
        }

        // --- CART LOGIC ---
        let cart = {};
        
        function updateCartUI() {
            document.querySelectorAll('.item-row').forEach(row => {
                const uuid = row.dataset.uuid;
                const qtyDisplay = row.querySelector('.qty-display');
                if (cart[uuid]) {
                    qtyDisplay.innerText = cart[uuid].quantity;
                    row.classList.add('border-success');
                } else {
                    qtyDisplay.innerText = '0';
                    row.classList.remove('border-success');
                }
            });
            
            // Generate hidden inputs for form submission
            const container = document.getElementById('cartHiddenInputs');
            container.innerHTML = '';
            let index = 0;
            for (const [uuid, item] of Object.entries(cart)) {
                container.innerHTML += `<input type="hidden" name="items[${index}][subcategory_uuid]" value="${uuid}">`;
                container.innerHTML += `<input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">`;
                const safeName = String(item.name).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                container.innerHTML += `<input type="hidden" name="items[${index}][name]" value="${safeName}">`;
                index++;
            }
        }

        document.querySelectorAll('.btn-plus').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const row = this.closest('.item-row');
                const uuid = row.dataset.uuid;
                const name = row.dataset.name;
                
                if (!cart[uuid]) {
                    cart[uuid] = { quantity: 0, name: name, image: row.querySelector('img') ? row.querySelector('img').src : null };
                }
                cart[uuid].quantity++;
                updateCartUI();
            });
        });

        document.querySelectorAll('.btn-minus').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const row = this.closest('.item-row');
                const uuid = row.dataset.uuid;
                
                if (cart[uuid] && cart[uuid].quantity > 0) {
                    cart[uuid].quantity--;
                    if (cart[uuid].quantity === 0) {
                        delete cart[uuid];
                    }
                }
                updateCartUI();
            });
        });

        // Category chip + search work together
        let activeFilter = 'All';
        function updateCatHero() {
            const chip = document.querySelector('.filter-btn.active');
            if (!chip) return;
            document.getElementById('catHeroTitle').textContent = chip.dataset.title || '';
            document.getElementById('catHeroSub').textContent = chip.dataset.sub || '';
            const img = document.getElementById('catHeroImg');
            if (chip.dataset.img) { img.src = chip.dataset.img; img.style.display = ''; } else { img.style.display = 'none'; }
        }
        function applyItemFilters() {
            updateCatHero();
            const term = (document.getElementById('itemSearch').value || '').toLowerCase();
            document.querySelectorAll('.item-row').forEach(row => {
                const okCat = activeFilter === 'All' || row.dataset.category === activeFilter;
                const okName = row.dataset.name.toLowerCase().includes(term);
                row.style.display = (okCat && okName) ? 'flex' : 'none';
            });
        }

        // Filter functionality
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                activeFilter = this.dataset.filter;
                applyItemFilters();
            });
        });
        
        // Auto-select the subcategory chosen on the previous page (?subcategory=uuid)
        const preUuid = new URLSearchParams(window.location.search).get('subcategory');
        if (preUuid) {
            const preRow = Array.from(document.querySelectorAll('.item-row')).find(r => r.dataset.uuid === preUuid);
            if (preRow) {
                cart[preUuid] = { quantity: 1, name: preRow.dataset.name, image: preRow.querySelector('img') ? preRow.querySelector('img').src : null };
                updateCartUI();
                // Open on the picked item's category: only that category's sub-items are listed
                activeFilter = preRow.dataset.category;
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.toggle('active', b.dataset.filter === activeFilter));
                applyItemFilters();
                const activeChip = document.querySelector('.filter-btn.active');
                if (activeChip && activeChip.scrollIntoView) activeChip.scrollIntoView({ inline: 'center', block: 'nearest' });
            }
        }

        // Open on a category chosen elsewhere (?category=uuid) - no separate category page any more
        const preCat = new URLSearchParams(window.location.search).get('category');
        if (preCat && !preUuid) {
            const chip = Array.from(document.querySelectorAll('.filter-btn')).find(b => b.dataset.filter === preCat);
            if (chip) { chip.click(); chip.scrollIntoView && chip.scrollIntoView({ inline: 'center', block: 'nearest' }); }
        }

        // Filter icon in the search box: sort the visible items A-Z / Z-A
        let sortAsc = true;
        document.getElementById('sortToggle').addEventListener('click', function () {
            const list = document.getElementById('itemsList');
            const rows = Array.from(list.querySelectorAll('.item-row'));
            rows.sort((a, b) => a.dataset.name.localeCompare(b.dataset.name) * (sortAsc ? 1 : -1));
            rows.forEach(r => list.appendChild(r));
            this.title = sortAsc ? 'Sorted A to Z (tap for Z to A)' : 'Sorted Z to A (tap for A to Z)';
            sortAsc = !sortAsc;
        });

        // Search functionality
        document.getElementById('itemSearch').addEventListener('input', applyItemFilters);

        // Navigation Logic
        function renderStep2Summary() {
            const esc = t => String(t).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
            const ids = Object.keys(cart).filter(k => cart[k] && cart[k].quantity > 0);
            document.getElementById('sumCount').textContent = ids.length + (ids.length === 1 ? ' Item' : ' Items');
            document.getElementById('sumItems').innerHTML = ids.map(k => {
                const it = cart[k];
                const img = it.image ? '<img src="' + esc(it.image) + '" alt="">' : '<i class="fa-solid fa-box text-success"></i>';
                return '<div class="sd-pick"><div class="ph">' + img + '</div><div><b>' + esc(it.name) + '</b><span>Qty: ' + it.quantity + '</span></div></div>';
            }).join('');
            document.getElementById('sumLocation').textContent = (document.getElementById('pickup_location').value || '-');
            document.getElementById('sumPin').textContent = (document.getElementById('pin_code').value || '-');
        }
        document.querySelectorAll('#periodPills .period-pill').forEach(p => p.addEventListener('click', function () {
            document.querySelectorAll('#periodPills .period-pill').forEach(x => x.classList.remove('active'));
            this.classList.add('active');
            ['Morning','Afternoon','Evening'].forEach(n => document.getElementById('collapse' + n).classList.toggle('show', n === this.dataset.period));
        }));

        const showStep = (step) => {
            if (Number(step) === 2) renderStep2Summary();
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
                    if(Object.keys(cart).length === 0) {
                        sdToast("Please select at least one item."); return;
                    }
                    if(!document.getElementById('pickup_location').value) {
                        sdToast("Please enter a pickup address."); return;
                    }
                    if(!/^[1-9][0-9]{5}$/.test(pinInput.value)) {
                        sdToast("Please enter a valid 6-digit PIN code."); pinInput.focus(); return;
                    }
                    if(pinServiceable !== true) {
                        if (pinServiceable === false) { openServiceRequest(); return; }
                        sdToast("Please wait while we check service availability for your PIN code."); return;
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
                label.style.borderColor = '#4A9A58';
                label.style.backgroundColor = '#f1f8f1';
            }

            let titleClass = (i === 0 || i === 1) ? 'fw-bolder' : 'fw-bold text-dark';
            let titleStyle = (i === 0 || i === 1) ? 'color: #3D8B4D; font-size:1rem;' : 'font-size:0.95rem;';
            let suggestionBadge = (i === 0 || i === 1) ? '<span class="badge ms-2 rounded-pill" style="background: rgba(61,139,77,0.1); color: #3D8B4D; font-size: 0.65rem; border: 1px solid rgba(61,139,77,0.2);">Suggested</span>' : '';

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
                
                customLabel.style.borderColor = '#4A9A58';
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
                selectedLabel.style.borderColor = '#4A9A58';
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

        // --- Service area (PIN code) check ---
        let pinServiceable = null; // null = unknown, true/false = checked
        const pinInput = document.getElementById('pin_code');
        const pinStatus = document.getElementById('pinStatus');
        let pinCheckSeq = 0;

        function renderPinStatus(ok, msg) {
            pinServiceable = ok;
            if (ok === null) { pinStatus.innerHTML = msg ? '<span class="text-muted">' + msg + '</span>' : ''; return; }
            pinStatus.innerHTML = ok
                ? '<span class="text-success fw-semibold"><i class="fa-solid fa-circle-check me-1"></i>' + msg + '</span>'
                : '<span class="text-danger fw-semibold"><i class="fa-solid fa-circle-xmark me-1"></i>' + msg + '</span>'
                  + (/^[1-9][0-9]{5}$/.test(pinInput.value) ? ' <a href="#" id="pinRequestLink" class="fw-semibold ms-1" style="color: var(--primary-green, #3D8B4D);">Contact us</a>' : '');
            const link = document.getElementById('pinRequestLink');
            if (link) link.addEventListener('click', function (e) { e.preventDefault(); openServiceRequest(); });
        }

        // --- "Service not available" contact form ---
        const srModalEl = document.getElementById('serviceRequestModal');
        const srForm = document.getElementById('serviceRequestForm');
        function openServiceRequest() {
            document.getElementById('srPin').value = pinInput.value;
            document.getElementById('srPinLabel').textContent = /^[1-9][0-9]{5}$/.test(pinInput.value) ? pinInput.value : 'your PIN code';
            const addr = document.getElementById('pickup_location').value;
            if (addr && !document.getElementById('srArea').value) document.getElementById('srArea').value = '';
            document.getElementById('srError').style.display = 'none';
            bootstrap.Modal.getOrCreateInstance(srModalEl).show();
        }
        srForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const err = document.getElementById('srError');
            err.style.display = 'none';
            const payload = {
                name: document.getElementById('srName').value.trim(),
                phone: document.getElementById('srPhone').value.trim(),
                email: document.getElementById('srEmail').value.trim(),
                pin_code: document.getElementById('srPin').value.trim(),
                area: document.getElementById('srArea').value.trim(),
                message: document.getElementById('srMessage').value.trim(),
            };
            const fail = (m) => { err.textContent = m; err.style.display = 'block'; };
            if (!payload.name) return fail('Please enter your name.');
            if (!/^[0-9+\-\s]{7,15}$/.test(payload.phone)) return fail('Please enter a valid phone number.');
            if (!/^[1-9][0-9]{5}$/.test(payload.pin_code)) return fail('Please enter a valid 6-digit PIN code.');
            if (payload.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(payload.email)) return fail('Please enter a valid email address.');

            const btn = document.getElementById('srSubmit');
            const label = btn.textContent;
            btn.disabled = true; btn.textContent = 'Submitting...';
            try {
                const res = await fetch("{{ url('/api/customer/service-request') }}", {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data && data.status === 1) {
                    bootstrap.Modal.getInstance(srModalEl).hide();
                    srForm.reset();
                    sdToast(data.message, 'success');
                } else {
                    fail((data && data.message) || 'Could not submit your request. Please try again.');
                }
            } catch (ex) {
                fail('Could not reach the server. Please check your internet connection and try again.');
            } finally {
                btn.disabled = false; btn.textContent = label;
            }
        });

        async function checkPin(pin) {
            if (!/^[1-9][0-9]{5}$/.test(pin)) {
                renderPinStatus(pin.length ? false : null, pin.length ? 'Enter a valid 6-digit PIN code.' : '');
                return pinServiceable;
            }
            const seq = ++pinCheckSeq;
            renderPinStatus(null, 'Checking service availability...');
            try {
                const res = await fetch("{{ url('/api/customer/check-pincode') }}", {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ pin_code: pin })
                });
                const data = await res.json();
                if (seq !== pinCheckSeq) return pinServiceable; // a newer check is running
                const ok = !!(data && data.status === 1 && data.data && data.data.serviceable);
                renderPinStatus(ok, (data && data.message) || (ok ? 'Service available.' : 'Service not available.'));
            } catch (e) {
                if (seq === pinCheckSeq) renderPinStatus(null, 'Could not check availability. Please try again.');
            }
            return pinServiceable;
        }

        pinInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
            if (this.value.length === 6) checkPin(this.value);
            else renderPinStatus(this.value.length ? false : null, this.value.length ? 'Enter a valid 6-digit PIN code.' : '');
        });
        if (pinInput.value) checkPin(pinInput.value);

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
                            const detectedPin = data && data.address && data.address.postcode ? String(data.address.postcode).replace(/\D/g, '').slice(0, 6) : '';
                            if (detectedPin.length === 6) { pinInput.value = detectedPin; checkPin(detectedPin); }
                            else { renderPinStatus(false, 'We could not detect your PIN code. Please enter it manually.'); pinInput.focus(); }
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

        const MAX_IMG_BYTES = 2 * 1024 * 1024;
        const MAX_IMG_COUNT = 10;
        const ALLOWED_IMG_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        const updateFileCount = function() {
            const display = document.getElementById('fileNameDisplay');
            const n = document.querySelectorAll('#hiddenImagesContainer input').length;
            if (display) display.innerText = n > 0 ? (n + (n === 1 ? ' image added' : ' images added')) : 'No file chosen';
        };

        // Image Upload via AJAX
        const handleImageUpload = async function(files) {
            if (!files || files.length === 0) return;

            const already = document.querySelectorAll('#hiddenImagesContainer input').length;
            const valid = [];
            let rejected = [];
            for (const f of files) {
                if (!ALLOWED_IMG_TYPES.includes(f.type)) {
                    rejected.push(`"${f.name}" is not a supported image (use JPG, PNG, GIF or WEBP).`);
                } else if (f.size > MAX_IMG_BYTES) {
                    rejected.push(`"${f.name}" is ${(f.size / 1048576).toFixed(1)} MB. Each image must be 2 MB or smaller.`);
                } else if (already + valid.length >= MAX_IMG_COUNT) {
                    rejected.push(`You can add up to ${MAX_IMG_COUNT} images only.`);
                    break;
                } else {
                    valid.push(f);
                }
            }
            if (rejected.length) sdToast(rejected[0] + (rejected.length > 1 ? ` (+${rejected.length - 1} more skipped)` : ''));
            if (valid.length === 0) return;

            const formData = new FormData();
            valid.forEach(f => formData.append('images[]', f));
            document.getElementById('uploadProgress').style.display = 'block';
            try {
                const res = await fetch("{{ url('/api/customer/upload-images') }}", { method: 'POST', body: formData, headers: { 'Accept': 'application/json' } });
                let data = {};
                try { data = await res.json(); } catch (e) {}
                if (res.ok && data.status === 1) {
                    data.data.paths.forEach(path => {
                        const wrapper = document.createElement('div'); wrapper.className = 'image-preview-wrapper';
                        const img = document.createElement('img'); img.src = "{{ asset('') }}" + path; img.alt = 'Uploaded image';
                        const rm = document.createElement('button'); rm.type = 'button'; rm.className = 'remove-img-btn'; rm.innerHTML = '&times;'; rm.setAttribute('aria-label', 'Remove image');
                        const hiddenInput = document.createElement('input'); hiddenInput.type = 'hidden'; hiddenInput.name = 'images[]'; hiddenInput.value = path;
                        rm.addEventListener('click', () => { wrapper.remove(); hiddenInput.remove(); updateFileCount(); });
                        wrapper.appendChild(img); wrapper.appendChild(rm);
                        document.getElementById('imagePreviewContainer').appendChild(wrapper);
                        document.getElementById('hiddenImagesContainer').appendChild(hiddenInput);
                    });
                    updateFileCount();
                    sdToast(`${data.data.paths.length} image(s) uploaded.`, 'success');
                } else {
                    let msg = 'Upload failed. Please try again.';
                    if (res.status === 413) msg = 'The images are too large. Each image must be 2 MB or smaller.';
                    else if (data.errors) msg = Object.values(data.errors).flat()[0] || msg;
                    else if (data.message) msg = data.message;
                    sdToast(msg);
                }
            } catch(e) { sdToast('Could not upload. Please check your internet connection and try again.'); console.error(e); }
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
                sdToast("Please select a date and time slot."); return;
            }
            // Populate Preview Section
            const prevItemsList = document.getElementById('prev-items-list');
            prevItemsList.innerHTML = '';
            let totalItems = 0;
            
            for (const [uuid, item] of Object.entries(cart)) {
                totalItems += item.quantity;
                let imgHtml = item.image 
                    ? `<img src="${item.image}" alt="" style="width:40px; height:40px; object-fit:contain;" class="rounded border p-1 me-3">`
                    : `<div style="width:40px; height:40px;" class="rounded border p-1 me-3 bg-light d-flex align-items-center justify-content-center"><i class="fa-solid fa-couch text-success"></i></div>`;
                    
                prevItemsList.innerHTML += `
                    <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                        <div class="d-flex align-items-center">
                            ${imgHtml}
                            <div>
                                <h6 class="mb-0 fw-bold">${item.name}</h6>
                            </div>
                        </div>
                        <div class="fw-bold bg-light px-3 py-1 rounded flex-shrink-0 ms-2" style="white-space: nowrap;">Qty: ${item.quantity}</div>
                    </div>
                `;
            }
            document.getElementById('prev-total-items').innerText = `${totalItems} Items`;
            document.getElementById('prev-address').innerText = document.getElementById('pickup_location').value + ' - ' + pinInput.value;
            
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
                prevImages.innerHTML += `<img src="{{ asset('') }}${inp.value}">`;
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
                    sdToast("Please select a date and time slot."); return;
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
