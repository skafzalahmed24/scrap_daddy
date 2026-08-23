@extends('layouts.app')

@section('title', 'Scrap Vehicle Details - Scrap Daddy')

@push('styles')
    @include('partials.customer.styles')
    <style>
        .vehicle-type-card {
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 120px;
        }
        .vehicle-type-card:hover {
            border-color: #2e7d32;
            background-color: #f1f8e9;
        }
        .vehicle-type-card.selected {
            border-color: #2e7d32;
            background-color: #e8f5e9;
            box-shadow: 0 0 0 2px rgba(46, 125, 50, 0.2);
        }
        .vehicle-type-card img {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
        }
        .vehicle-type-card span {
            display: block;
            font-weight: 600;
            font-size: 0.9rem;
            color: #333;
        }
        .form-label {
            font-weight: 600;
            color: #555;
            font-size: 0.95rem;
            margin-bottom: 8px;
        }
        .form-control {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 0.95rem;
        }
        .form-control:focus {
            border-color: #2e7d32;
            box-shadow: 0 0 0 0.2rem rgba(46, 125, 50, 0.1);
        }
        .photo-upload-area {
            border: 1px dashed #ccc;
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            background-color: #f9f9f9;
            cursor: pointer;
            transition: all 0.2s;
        }
        .photo-upload-area:hover {
            background-color: #f1f8e9;
            border-color: #2e7d32;
        }
        .photo-upload-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #e8f5e9;
            color: #2e7d32;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        .photo-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        .photo-preview {
            position: relative;
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #ddd;
        }
        .photo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .remove-photo {
            position: absolute;
            top: 4px;
            right: 4px;
            background: rgba(0,0,0,0.6);
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
<div class="dashboard-container">
    <div class="row g-4">
        @include('partials.customer.sidebar')

        <!-- Main Content -->
        <div class="col-xl-9 col-lg-9">
            <div class="bg-white rounded-4 shadow-sm p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark">Vehicle details</h5>
                    <i class="fa-solid fa-circle-info text-muted fs-5"></i>
                </div>

                <div id="alertContainer"></div>

                <form id="scrapVehicleForm">
                    <!-- Vehicle Type -->
                    <div class="mb-4">
                        <label class="form-label">Vehicle Type</label>
                        <div class="d-flex gap-3 flex-wrap">
                            <div class="vehicle-type-card selected" onclick="selectVehicleType(this, 'Heavy Load')">
                                <!-- Placeholder for heavy truck icon -->
                                <img src="https://cdn-icons-png.flaticon.com/512/2830/2830312.png" alt="Heavy Load">
                                <span>Heavy Load</span>
                            </div>
                            <div class="vehicle-type-card" onclick="selectVehicleType(this, 'Light Commercial')">
                                <img src="https://cdn-icons-png.flaticon.com/512/1008/1008064.png" alt="Light Commercial">
                                <span>Light Commercial</span>
                            </div>
                            <div class="vehicle-type-card" onclick="selectVehicleType(this, 'Car')">
                                <img src="https://cdn-icons-png.flaticon.com/512/3204/3204005.png" alt="Car">
                                <span>Car</span>
                            </div>
                            <div class="vehicle-type-card" onclick="selectVehicleType(this, 'Two Wheeler')">
                                <img src="https://cdn-icons-png.flaticon.com/512/1769/1769747.png" alt="Two Wheeler">
                                <span>Two Wheeler</span>
                            </div>
                        </div>
                        <input type="hidden" name="vehicle_type" id="vehicle_type" value="Heavy Load">
                    </div>

                    <!-- Vehicle Number -->
                    <div class="mb-4">
                        <label for="vehicle_number" class="form-label">Vehicle number</label>
                        <input type="text" class="form-control" id="vehicle_number" name="vehicle_number" placeholder="eg. MP04 DK 9834" required>
                    </div>

                    <!-- Vehicle Brand -->
                    <div class="mb-4">
                        <label for="vehicle_brand" class="form-label">Vehicle brand (optional)</label>
                        <input type="text" class="form-control" id="vehicle_brand" name="vehicle_brand" placeholder="eg. Honda, Toyota, Tata etc.">
                    </div>

                    <!-- Vehicle Model -->
                    <div class="mb-4">
                        <label for="vehicle_model" class="form-label">Vehicle model (optional)</label>
                        <input type="text" class="form-control" id="vehicle_model" name="vehicle_model" placeholder="eg. Civic 2019">
                    </div>

                    <!-- Upload Photo -->
                    <div class="mb-4">
                        <label class="form-label">Upload Photo</label>
                        <div class="photo-upload-area" id="photoUploadArea">
                            <div class="photo-upload-icon">
                                <i class="fa-solid fa-camera-retro"></i>
                            </div>
                            <h6 class="fw-bold mb-1">Tap to add photos</h6>
                            <small class="text-muted">JPG, PNG &bull; Max 5 photos &bull; 15 MB each</small>
                            <input type="file" id="fileInput" multiple accept="image/jpeg, image/png" class="d-none">
                        </div>
                        <div class="photo-preview-container" id="photoPreviewContainer"></div>
                        <div id="uploadedPhotosInputs"></div> <!-- hidden inputs for uploaded photo paths -->
                    </div>

                    <!-- Remark -->
                    <div class="mb-5">
                        <label for="remark" class="form-label">Remark</label>
                        <textarea class="form-control" id="remark" name="remark" rows="3" placeholder="Add your remark here"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-lg text-white rounded-pill fw-bold" id="submitBtn" style="background-color: #2e7d32;">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
<script>
    // Vehicle Type Selection
    function selectVehicleType(element, type) {
        document.querySelectorAll('.vehicle-type-card').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');
        document.getElementById('vehicle_type').value = type;
    }

    // Photo Upload Logic
    const photoUploadArea = document.getElementById('photoUploadArea');
    const fileInput = document.getElementById('fileInput');
    const photoPreviewContainer = document.getElementById('photoPreviewContainer');
    const uploadedPhotosInputs = document.getElementById('uploadedPhotosInputs');
    let uploadedPhotos = [];

    photoUploadArea.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', async function(e) {
        const files = Array.from(e.target.files);
        if(uploadedPhotos.length + files.length > 5) {
            Swal.fire('Limit Exceeded', 'You can only upload up to 5 photos.', 'warning');
            return;
        }

        // We will call the upload API for each file
        const uploadBtnIcon = photoUploadArea.querySelector('.photo-upload-icon i');
        uploadBtnIcon.className = 'fa-solid fa-spinner fa-spin';

        for (const file of files) {
            if (file.size > 15 * 1024 * 1024) {
                Swal.fire('File too large', `File ${file.name} exceeds 15MB limit.`, 'error');
                continue;
            }

            try {
                const formData = new FormData();
                formData.append('images[]', file);

                const response = await fetch('/api/customer/upload-images', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '',
                        'Authorization': 'Bearer ' + localStorage.getItem('auth_token')
                    }
                });

                const data = await response.json();
                if (data.status === 1 && data.data && data.data.paths && data.data.paths.length > 0) {
                    const imgPath = data.data.paths[0];
                    uploadedPhotos.push(imgPath);
                    renderPhotoPreview(imgPath);
                } else {
                    console.error('Upload failed', data);
                }
            } catch (error) {
                console.error('Error uploading file:', error);
            }
        }
        
        uploadBtnIcon.className = 'fa-solid fa-camera-retro';
        fileInput.value = ''; // Reset
    });

    function renderPhotoPreview(imgPath) {
        const index = uploadedPhotos.indexOf(imgPath);
        const div = document.createElement('div');
        div.className = 'photo-preview';
        div.innerHTML = `
            <img src="/${imgPath}" alt="Uploaded photo">
            <button type="button" class="remove-photo" onclick="removePhoto('${imgPath}', this)"><i class="fa-solid fa-xmark"></i></button>
        `;
        photoPreviewContainer.appendChild(div);
        
        // Add hidden input
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'photos[]';
        input.value = imgPath;
        input.id = 'input_' + imgPath.replace(/[^a-zA-Z0-9]/g, '');
        uploadedPhotosInputs.appendChild(input);
    }

    function removePhoto(imgPath, btn) {
        const index = uploadedPhotos.indexOf(imgPath);
        if (index > -1) {
            uploadedPhotos.splice(index, 1);
        }
        // Remove preview
        btn.closest('.photo-preview').remove();
        // Remove hidden input
        const inputId = 'input_' + imgPath.replace(/[^a-zA-Z0-9]/g, '');
        const input = document.getElementById(inputId);
        if (input) input.remove();
    }

    // Form Submission
    document.getElementById('scrapVehicleForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerText;
        submitBtn.disabled = true;
        submitBtn.innerText = 'Submitting...';

        const formData = new FormData(this);

        try {
            const response = await fetch('{{ route("customer.scrap_vehicles.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (response.ok && data.status === 1) {
                Swal.fire({
                    icon: 'success',
                    title: 'Submitted Successfully',
                    text: 'Your scrap vehicle details have been recorded.',
                    confirmButtonColor: '#2e7d32'
                }).then(() => {
                    window.location.href = "{{ route('customer.home') }}";
                });
            } else {
                let errorHtml = data.message || 'Validation failed';
                if (data.data && data.data.errors) {
                    errorHtml += '<ul>';
                    for (const [key, msgs] of Object.entries(data.data.errors)) {
                        msgs.forEach(msg => {
                            errorHtml += `<li>${msg}</li>`;
                        });
                    }
                    errorHtml += '</ul>';
                }
                
                document.getElementById('alertContainer').innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show">
                        ${errorHtml}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Submit error:', error);
            Swal.fire('Error', 'An unexpected error occurred.', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerText = originalText;
        }
    });
</script>
@endpush
