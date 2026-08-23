@extends('layouts.admin')

@section('title', 'Reward Configurations')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/categories.css') }}">
    <!-- SweetAlert2 for notifications -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
@endpush

@section('content')
<h1 class="page-title mb-3">Rewards & Configurations</h1>

<!-- Global Settings Card -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold">Global Settings</h5>
    </div>
    <div class="card-body">
        <form id="settingsForm" class="d-flex align-items-end gap-3">
            <div>
                <label for="coin_value_in_rupees" class="form-label text-muted small mb-1">Coin Value in Rupees</label>
                <div class="input-group">
                    <span class="input-group-text bg-white">1 Coin = ₹</span>
                    <input type="number" step="0.01" class="form-control" id="coin_value_in_rupees" required>
                </div>
                <small class="text-muted d-block mt-1">Example: 0.10 means 10 coins = 1 Rupee</small>
            </div>
            <button type="submit" class="btn btn-primary" id="saveSettingsBtn">Save Settings</button>
        </form>
    </div>
</div>

<h5 class="fw-bold mb-3">Reward Tiers</h5>

<div class="d-flex flex-column flex-md-row gap-3 mb-4 justify-content-end">
    <button class="btn btn-primary text-nowrap px-4" onclick="openModal('add')">
        <i class="fa-solid fa-plus me-2"></i> Add New Tier
    </button>
</div>

<div class="table-container">
    <div class="table-responsive" style="max-height: calc(100vh - 450px); overflow-y: auto;">
        <table class="table table-hover align-middle mb-0" style="min-width: 600px;">
            <thead class="sticky-top bg-white" style="z-index: 10;">
                <tr>
                    <th style="width: 5%">ID</th>
                    <th style="width: 25%">Min Amount (₹)</th>
                    <th style="width: 25%">Max Amount (₹)</th>
                    <th style="width: 20%">Reward Coins</th>
                    <th style="width: 15%">Validity (Days)</th>
                    <th style="width: 10%">Status</th>
                    <th class="text-end" style="width: 15%">Actions</th>
                </tr>
            </thead>
            <tbody id="configsTableBody">
                <tr>
                    <td colspan="6" class="text-center py-4">Loading configurations...</td>
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
<div class="offcanvas offcanvas-end" tabindex="-1" id="configOffcanvas" aria-labelledby="configOffcanvasLabel" style="width: 500px; max-width: 100vw;">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title" id="configOffcanvasLabel">Add Reward Tier</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
      <form id="configForm">
          <input type="hidden" id="configId">
          
          <div class="mb-3">
              <label for="min_amount" class="form-label">Min Amount (₹) <span class="text-danger">*</span></label>
              <input type="number" step="0.01" class="form-control" id="min_amount" name="min_amount" required>
          </div>
          
          <div class="mb-3">
              <label for="max_amount" class="form-label">Max Amount (₹) <span class="text-danger">*</span></label>
              <input type="number" step="0.01" class="form-control" id="max_amount" name="max_amount" required>
          </div>
          
          <div class="mb-3">
              <label for="reward_coins" class="form-label">Reward Coins <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="reward_coins" name="reward_coins" required>
          </div>
          
          <div class="mb-3">
              <label for="validity_days" class="form-label">Validity (Days)</label>
              <input type="number" class="form-control" id="validity_days" name="validity_days" placeholder="Leave empty for lifetime validity">
          </div>
          
          <div class="mb-3 form-check form-switch">
              <input class="form-check-input" type="checkbox" id="status" name="status" checked>
              <label class="form-check-label" for="status">Active</label>
          </div>
          
          <div class="mt-4">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="offcanvas">Cancel</button>
            <button type="submit" class="btn btn-primary" id="saveBtn">Save Tier</button>
          </div>
      </form>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
<script>
    const API_URL = '/api/rewards';
    let currentPage = 1;
    const configOffcanvas = new bootstrap.Offcanvas(document.getElementById('configOffcanvas'));
    
    document.addEventListener('DOMContentLoaded', () => {
        fetchSettings();
        fetchConfigs(1);
    });

    async function fetchSettings() {
        try {
            const response = await fetch('/api/rewards/settings');
            const data = await response.json();
            if(data && data.coin_value_in_rupees) {
                document.getElementById('coin_value_in_rupees').value = data.coin_value_in_rupees;
            }
        } catch (error) {
            console.error('Failed to load settings', error);
        }
    }

    document.getElementById('settingsForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const saveBtn = document.getElementById('saveSettingsBtn');
        saveBtn.disabled = true;
        saveBtn.innerText = 'Saving...';

        const formData = new FormData();
        formData.append('coin_value_in_rupees', document.getElementById('coin_value_in_rupees').value);

        try {
            const response = await fetch('/api/rewards/settings', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            
            if(response.ok) {
                Swal.fire({toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000});
            } else {
                Swal.fire('Error', data.message || 'Error updating settings', 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'An unexpected error occurred', 'error');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerText = 'Save Settings';
        }
    });

    async function fetchConfigs(page = 1) {
        currentPage = page;
        try {
            const response = await fetch(`${API_URL}?page=${page}`);
            const data = await response.json();
            renderTable(data.data);
            renderPagination(data);
        } catch (error) {
            Swal.fire('Error', 'Failed to load configurations', 'error');
        }
    }

    function renderTable(configs) {
        const tbody = document.getElementById('configsTableBody');
        tbody.innerHTML = '';
        
        if(configs.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4">No configurations found.</td></tr>`;
            return;
        }

        configs.forEach(config => {
            const statusBadge = `
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" onchange="toggleStatus('${config.id}', this.checked)" ${config.status ? 'checked' : ''}>
                </div>
            `;
                
            const row = `
                <tr>
                    <td>${config.id}</td>
                    <td class="fw-semibold">₹${parseFloat(config.min_amount).toFixed(2)}</td>
                    <td class="fw-semibold">₹${parseFloat(config.max_amount).toFixed(2)}</td>
                    <td class="fw-bold text-success"><i class="fa-solid fa-coins me-1"></i> ${config.reward_coins}</td>
                    <td>${config.validity_days ? config.validity_days + ' Days' : '<span class="text-muted">Lifetime</span>'}</td>
                    <td>${statusBadge}</td>
                    <td class="text-end text-nowrap">
                        <button class="btn btn-sm btn-light text-brand me-1" onclick='openModal("edit", ${JSON.stringify(config).replace(/'/g, "&#39;")})'>
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button class="btn btn-sm btn-light text-danger" onclick="deleteConfig('${config.id}')">
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
                    <button class="page-link" onclick="fetchConfigs(${pageNum})" ${link.url === null ? 'disabled' : ''}>
                        ${link.label}
                    </button>
                </li>
            `;
            ul.insertAdjacentHTML('beforeend', li);
        });
    }

    function openModal(mode, config = null) {
        const form = document.getElementById('configForm');
        form.reset();
        
        if (mode === 'add') {
            document.getElementById('configOffcanvasLabel').innerText = 'Add Reward Tier';
            document.getElementById('configId').value = '';
            document.getElementById('status').checked = true;
            document.getElementById('validity_days').value = '';
        } else {
            document.getElementById('configOffcanvasLabel').innerText = 'Edit Reward Tier';
            document.getElementById('configId').value = config.id;
            document.getElementById('min_amount').value = parseFloat(config.min_amount).toFixed(2);
            document.getElementById('max_amount').value = parseFloat(config.max_amount).toFixed(2);
            document.getElementById('reward_coins').value = config.reward_coins;
            document.getElementById('validity_days').value = config.validity_days || '';
            document.getElementById('status').checked = config.status == 1;
        }
        configOffcanvas.show();
    }

    document.getElementById('configForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('configId').value;
        const formData = new FormData();
        
        formData.append('min_amount', document.getElementById('min_amount').value);
        formData.append('max_amount', document.getElementById('max_amount').value);
        formData.append('reward_coins', document.getElementById('reward_coins').value);
        formData.append('validity_days', document.getElementById('validity_days').value);
        formData.append('status', document.getElementById('status').checked ? 1 : 0);
        
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
                configOffcanvas.hide();
                fetchConfigs(currentPage);
            } else {
                Swal.fire('Error', data.message || 'Validation Error', 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'An unexpected error occurred', 'error');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerText = 'Save Tier';
        }
    });

    function deleteConfig(id) {
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
                        fetchConfigs(currentPage);
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error!', 'Failed to delete configuration.', 'error');
                }
            }
        })
    }

    async function toggleStatus(id, isChecked) {
        try {
            const formData = new FormData();
            formData.append('status', isChecked ? 1 : 0);
            
            const response = await fetch(`${API_URL}/${id}/status`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            
            if(response.ok) {
                Swal.fire({toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000});
            } else {
                Swal.fire('Error', data.message || 'Error updating status', 'error');
                fetchConfigs(currentPage);
            }
        } catch (error) {
            Swal.fire('Error', 'An unexpected error occurred', 'error');
            fetchConfigs(currentPage);
        }
    }
</script>
@endpush
