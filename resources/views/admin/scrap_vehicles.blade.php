@extends('layouts.admin')

@section('title', 'Scrap Vehicles')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
    <style>
        .vehicle-img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        .table-container {
            background: #fff;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
    </style>
@endpush

@section('content')
<h1 class="page-title mb-3">Scrap Vehicles</h1>

<div class="d-flex flex-column flex-md-row gap-3 mb-4">
    <div class="input-group bg-white rounded" style="flex: 1; border: 1px solid var(--border-color);">
        <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" id="searchInput" class="form-control border-0 ps-0 py-2" placeholder="Search by Vehicle Number, Brand, Type..." style="box-shadow: none;">
    </div>
    <select id="statusFilter" class="form-select w-auto">
        <option value="">All Statuses</option>
        <option value="pending">Pending</option>
        <option value="completed">Completed</option>
    </select>
</div>

<div class="table-container">
    <div class="table-responsive" style="max-height: calc(100vh - 250px); overflow-y: auto;">
        <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
            <thead class="sticky-top bg-white" style="z-index: 10;">
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Vehicle Details</th>
                    <th>Remark</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="vehiclesTableBody">
                <tr>
                    <td colspan="7" class="text-center py-4">Loading vehicles...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="vehicleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Vehicle Photos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="photosContainer" class="d-flex flex-wrap gap-2">
            <!-- photos injected here -->
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
<script>
    const API_URL = '/api/admin/scrap-vehicles';
    const STATUS_API_URL = '/api/admin/scrap-vehicles/status';
    
    let searchQuery = '';
    let statusQuery = '';

    // Load data on page load
    document.addEventListener('DOMContentLoaded', () => fetchVehicles());

    // Search and Filter
    let searchTimeout = null;
    document.getElementById('searchInput').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchQuery = e.target.value;
        searchTimeout = setTimeout(() => {
            fetchVehicles();
        }, 400); 
    });

    document.getElementById('statusFilter').addEventListener('change', function(e) {
        statusQuery = e.target.value;
        fetchVehicles();
    });

    // Fetch Vehicles
    async function fetchVehicles() {
        try {
            const formData = new FormData();
            if(searchQuery) formData.append('search', searchQuery);
            if(statusQuery) formData.append('status', statusQuery);

            const response = await fetch(API_URL, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            
            if (data.status === 1) {
                renderTable(data.data.rows);
            } else {
                Swal.fire('Error', data.message || 'Failed to load data', 'error');
            }
        } catch (error) {
            console.error('Error fetching vehicles:', error);
            Swal.fire('Error', 'Failed to load vehicles', 'error');
        }
    }

    // Render Table
    function renderTable(vehicles) {
        const tbody = document.getElementById('vehiclesTableBody');
        tbody.innerHTML = '';
        
        if(vehicles.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4">No scrap vehicles found.</td></tr>`;
            return;
        }

        vehicles.forEach(v => {
            const statusBadge = v.status === 'completed' 
                ? `<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Completed</span>` 
                : `<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">Pending</span>`;
            
            const customerInfo = v.user ? `<div><strong>${v.user.full_name}</strong></div><small class="text-muted">${v.user.phone_number}</small>` : 'Unknown';
            
            const dateStr = new Date(v.created_at).toLocaleDateString();

            const photosJson = JSON.stringify(v.photos || []).replace(/"/g, '&quot;');

            let actions = '';
            if (v.status === 'pending') {
                actions = `<button class="btn btn-sm btn-success text-nowrap" onclick="updateStatus(${v.id}, 'completed')">
                                <i class="fa-solid fa-check me-1"></i> Mark Completed
                           </button>`;
            } else {
                actions = `<button class="btn btn-sm btn-outline-secondary text-nowrap" onclick="updateStatus(${v.id}, 'pending')">
                                <i class="fa-solid fa-rotate-left me-1"></i> Revert
                           </button>`;
            }

            const row = `
                <tr>
                    <td>#${v.id}</td>
                    <td>${customerInfo}</td>
                    <td>
                        <div class="fw-semibold text-primary">${v.vehicle_number}</div>
                        <div class="small">${v.vehicle_type}</div>
                        ${v.vehicle_brand || v.vehicle_model ? `<div class="small text-muted">${v.vehicle_brand || ''} ${v.vehicle_model || ''}</div>` : ''}
                        ${v.photos && v.photos.length > 0 ? `<a href="#" onclick="viewPhotos(${photosJson})" class="small text-decoration-none mt-1 d-inline-block"><i class="fa-solid fa-image me-1"></i> View Photos (${v.photos.length})</a>` : ''}
                    </td>
                    <td>${v.remark || '<span class="text-muted">-</span>'}</td>
                    <td>${statusBadge}</td>
                    <td>${dateStr}</td>
                    <td class="text-end">
                        ${actions}
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }

    async function updateStatus(id, newStatus) {
        try {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('status', newStatus);

            const response = await fetch(STATUS_API_URL, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            
            if (data.status === 1) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Status updated',
                    showConfirmButton: false,
                    timer: 1500
                });
                fetchVehicles();
            } else {
                Swal.fire('Error', data.message || 'Failed to update status', 'error');
            }
        } catch (error) {
            console.error('Error updating status:', error);
            Swal.fire('Error', 'Failed to update status', 'error');
        }
    }

    function viewPhotos(photos) {
        const container = document.getElementById('photosContainer');
        container.innerHTML = '';
        if (photos.length === 0) {
            container.innerHTML = '<p class="text-muted">No photos available.</p>';
        } else {
            photos.forEach(p => {
                const img = document.createElement('img');
                img.src = '/' + p;
                img.className = 'img-thumbnail';
                img.style.maxWidth = '200px';
                img.style.maxHeight = '200px';
                img.style.objectFit = 'cover';
                container.appendChild(img);
            });
        }
        const modal = new bootstrap.Modal(document.getElementById('vehicleModal'));
        modal.show();
    }
</script>
@endpush
