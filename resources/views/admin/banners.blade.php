@extends('layouts.admin')

@section('title', 'Banners')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/categories.css') }}">
    <!-- SweetAlert2 for notifications -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
    <style>
        .banner-media {
            max-width: 80px;
            max-height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
@endpush

@section('content')
<h1 class="page-title mb-3">Banners</h1>

<div class="d-flex flex-column flex-md-row gap-3 mb-4">
    <div class="input-group bg-white rounded" style="flex: 1; border: 1px solid var(--border-color);">
        <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" id="searchInput" class="form-control border-0 ps-0 py-2" placeholder="Search by Title..." style="box-shadow: none;">
    </div>
    <button class="btn btn-primary text-nowrap px-4" onclick="openModal('add')">
        <i class="fa-solid fa-plus me-2"></i> Add New
    </button>
</div>

<div class="table-container">
    <div class="table-responsive" style="max-height: calc(100vh - 350px); overflow-y: auto;">
        <table class="table table-hover align-middle mb-0" style="min-width: 600px;">
            <thead class="sticky-top bg-white" style="z-index: 10;">
                <tr>
                    <th>Media</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="bannersTableBody">
                <tr>
                    <td colspan="5" class="text-center py-4">Loading banners...</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="pagination-container" id="paginationContainer">
        <span class="text-muted" id="pageInfo">Showing 0 to 0 of 0 entries</span>
        <nav>
            <ul class="pagination mb-0" id="paginationLinks">
            </ul>
        </nav>
    </div>
</div>

<!-- Add/Edit Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="bannerOffcanvas" aria-labelledby="bannerOffcanvasLabel" style="width: 400px; max-width: 100vw;">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title" id="bannerOffcanvasLabel">Add Banner</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
      <form id="bannerForm">
          <input type="hidden" id="bannerId">
          
          <div class="mb-3">
              <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="title" name="title" required>
          </div>
          
          <div class="mb-3">
              <label for="short_description" class="form-label">Short Description</label>
              <textarea class="form-control" id="short_description" name="short_description" rows="3"></textarea>
          </div>
          
          <div class="mb-3">
              <label for="uploads" class="form-label">Uploads (Image or Video)</label>
              <input type="file" class="form-control" id="uploads" name="uploads" accept="image/*,video/*">
              <div class="mt-2" id="currentMediaContainer" style="display:none;">
                  <p class="mb-1 text-muted small">Current File:</p>
                  <div id="mediaPreview"></div>
              </div>
          </div>
          
          <div class="mb-3 form-check form-switch">
              <input class="form-check-input" type="checkbox" id="status" name="status" checked>
              <label class="form-check-label" for="status">Active</label>
          </div>
          
          <div class="mt-4">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="offcanvas">Cancel</button>
            <button type="submit" class="btn btn-primary" id="saveBtn">Save Banner</button>
          </div>
      </form>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
<script>
    const API_URL = '/api/banners';
    let currentPage = 1;
    let searchQuery = '';
    const bannerOffcanvas = new bootstrap.Offcanvas(document.getElementById('bannerOffcanvas'));
    
    document.addEventListener('DOMContentLoaded', () => fetchBanners(1));

    let searchTimeout = null;
    document.getElementById('searchInput').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchQuery = e.target.value;
        searchTimeout = setTimeout(() => {
            fetchBanners(1);
        }, 400);
    });

    async function fetchBanners(page = 1) {
        currentPage = page;
        try {
            const url = `${API_URL}?page=${page}&search=${encodeURIComponent(searchQuery)}`;
            const response = await fetch(url);
            const data = await response.json();
            renderTable(data.data);
            renderPagination(data);
        } catch (error) {
            Swal.fire('Error', 'Failed to load banners', 'error');
        }
    }

    function renderTable(banners) {
        const tbody = document.getElementById('bannersTableBody');
        tbody.innerHTML = '';
        
        if(banners.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4">No banners found.</td></tr>`;
            return;
        }

        banners.forEach(banner => {
            let mediaHtml = '<span class="text-muted">No Media</span>';
            if (banner.uploads) {
                const isVideo = banner.uploads.match(/\.(mp4|webm|ogg)$/i);
                if (isVideo) {
                    mediaHtml = `<video src="/${banner.uploads}" class="banner-media" muted controls style="background: #000;"></video>`;
                } else {
                    mediaHtml = `<img src="/${banner.uploads}" class="banner-media border" alt="${banner.title}">`;
                }
            }

            const statusBadge = banner.status 
                ? `<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Active</span>` 
                : `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">Inactive</span>`;
                
            const row = `
                <tr>
                    <td>${mediaHtml}</td>
                    <td class="fw-semibold">${banner.title}</td>
                    <td class="text-truncate" style="max-width: 200px;">${banner.short_description || '-'}</td>
                    <td>${statusBadge}</td>
                    <td class="text-end text-nowrap">
                        <button class="btn btn-sm btn-light text-brand me-1" onclick='openModal("edit", ${JSON.stringify(banner).replace(/'/g, "&#39;")})'>
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button class="btn btn-sm btn-light text-danger" onclick="deleteBanner('${banner.uuid}')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }

    function renderPagination(data) {
        document.getElementById('pageInfo').innerText = `Showing ${data.from || 0} to ${data.to || 0} of ${data.total} entries`;
        const ul = document.getElementById('paginationLinks');
        ul.innerHTML = '';
        data.links.forEach(link => {
            const activeClass = link.active ? 'active' : '';
            const disabledClass = link.url === null ? 'disabled' : '';
            let pageNum = 1;
            if(link.url) {
                const urlObj = new URL(link.url, window.location.origin);
                pageNum = urlObj.searchParams.get('page');
            }
            const li = `
                <li class="page-item ${activeClass} ${disabledClass}">
                    <button class="page-link" onclick="fetchBanners(${pageNum})" ${link.url === null ? 'disabled' : ''}>
                        ${link.label}
                    </button>
                </li>
            `;
            ul.insertAdjacentHTML('beforeend', li);
        });
    }

    function openModal(mode, banner = null) {
        const form = document.getElementById('bannerForm');
        form.reset();
        document.getElementById('currentMediaContainer').style.display = 'none';
        
        if (mode === 'add') {
            document.getElementById('bannerOffcanvasLabel').innerText = 'Add Banner';
            document.getElementById('bannerId').value = '';
            document.getElementById('status').checked = true;
        } else {
            document.getElementById('bannerOffcanvasLabel').innerText = 'Edit Banner';
            document.getElementById('bannerId').value = banner.uuid;
            document.getElementById('title').value = banner.title;
            document.getElementById('short_description').value = banner.short_description || '';
            document.getElementById('status').checked = banner.status == 1;
            
            if(banner.uploads) {
                document.getElementById('currentMediaContainer').style.display = 'block';
                const isVideo = banner.uploads.match(/\.(mp4|webm|ogg)$/i);
                if (isVideo) {
                    document.getElementById('mediaPreview').innerHTML = `<video src="/${banner.uploads}" style="max-width:100%; max-height: 150px; background: #000;" controls></video>`;
                } else {
                    document.getElementById('mediaPreview').innerHTML = `<img src="/${banner.uploads}" style="max-width:100%; max-height: 150px;" class="border">`;
                }
            }
        }
        bannerOffcanvas.show();
    }

    document.getElementById('bannerForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('bannerId').value;
        const formData = new FormData();
        
        formData.append('title', document.getElementById('title').value);
        formData.append('short_description', document.getElementById('short_description').value);
        formData.append('status', document.getElementById('status').checked ? 1 : 0);
        
        const file = document.getElementById('uploads').files[0];
        if (file) {
            formData.append('uploads', file);
        }
        
        const isEdit = id !== '';
        const url = isEdit ? `${API_URL}/${id}` : API_URL;
        
        const saveBtn = document.getElementById('saveBtn');
        saveBtn.disabled = true;
        saveBtn.innerText = 'Saving...';

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            
            if(response.ok) {
                Swal.fire('Success', data.message, 'success');
                bannerOffcanvas.hide();
                fetchBanners(currentPage);
            } else {
                Swal.fire('Error', data.message || 'Validation Error', 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'An unexpected error occurred', 'error');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerText = 'Save Banner';
        }
    });

    function deleteBanner(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch(`${API_URL}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    
                    if(response.ok) {
                        Swal.fire('Deleted!', data.message, 'success');
                        fetchBanners(currentPage);
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error!', 'Failed to delete banner.', 'error');
                }
            }
        })
    }
</script>
@endpush
