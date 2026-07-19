@extends('layouts.admin')

@section('title', 'Subcategories')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/categories.css') }}">
    <!-- SweetAlert2 for notifications -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
@endpush

@section('content')
<h1 class="page-title mb-3">Subcategories</h1>

<div class="d-flex flex-column flex-md-row gap-3 mb-4">
    <div class="input-group bg-white rounded" style="flex: 1; border: 1px solid var(--border-color);">
        <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" id="searchInput" class="form-control border-0 ps-0 py-2" placeholder="Search by Title, etc." style="box-shadow: none;">
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
                    <th>Image</th>
                    <th>Category</th>
                    <th>Subcategory Title</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="categoriesTableBody">
                <!-- Categories injected via JS -->
                <tr>
                    <td colspan="5" class="text-center py-4">Loading subcategories...</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="pagination-container" id="paginationContainer">
        <span class="text-muted" id="pageInfo">Showing 0 to 0 of 0 entries</span>
        <nav>
            <ul class="pagination mb-0" id="paginationLinks">
                <!-- Links injected via JS -->
            </ul>
        </nav>
    </div>
</div>

<!-- Add/Edit Offcanvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="categoryOffcanvas" aria-labelledby="categoryOffcanvasLabel" style="width: 400px; max-width: 100vw;">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title" id="categoryOffcanvasLabel">Add Subcategory</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
      <form id="categoryForm">
          <input type="hidden" id="categoryId">

          <div class="mb-3">
              <label for="category_id" class="form-label">Parent Category <span class="text-danger">*</span></label>
              <select class="form-select" id="category_id" name="category_id" required>
                  <option value="">Select Category...</option>
              </select>
          </div>
          
          <div class="mb-3">
              <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="title" name="name" required>
          </div>
          
          <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" id="description" name="description" rows="3"></textarea>
          </div>
          
          <div class="mb-3">
              <label for="image" class="form-label">Image</label>
              <input type="file" class="form-control" id="image" name="image" accept="image/*">
              <div class="mt-2" id="currentImageContainer" style="display:none;">
                  <p class="mb-1 text-muted small">Current Image:</p>
                  <img src="" id="currentImage" class="category-img border">
              </div>
          </div>
          
          <div class="mb-3 form-check form-switch">
              <input class="form-check-input" type="checkbox" id="status" name="status" checked>
              <label class="form-check-label" for="status">Active</label>
          </div>
          
          <div class="mt-4">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="offcanvas">Cancel</button>
            <button type="submit" class="btn btn-primary" id="saveBtn">Save Category</button>
          </div>
      </form>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
<script>
    const API_URL = '/api/subcategories';
    const CAT_API_URL = '/api/categories';
    let currentPage = 1;
    let searchQuery = '';
    const categoryOffcanvas = new bootstrap.Offcanvas(document.getElementById('categoryOffcanvas'));
    
    // Load data on page load
    document.addEventListener('DOMContentLoaded', () => {
        fetchSubcategories(1);
        fetchParentCategories();
    });

    async function fetchParentCategories() {
        try {
            const res = await fetch(CAT_API_URL + '?limit=100');
            const data = await res.json();
            const select = document.getElementById('category_id');
            select.innerHTML = '<option value="">Select Category...</option>';
            if(data.data) {
                data.data.forEach(cat => {
                    select.innerHTML += `<option value="${cat.uuid}">${cat.title}</option>`;
                });
            }
        } catch(e) {
            console.error('Failed to load categories');
        }
    }

    // Handle Search input
    let searchTimeout = null;
    document.getElementById('searchInput').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchQuery = e.target.value;
        searchTimeout = setTimeout(() => {
            fetchSubcategories(1);
        }, 400);
    });

    // Fetch Subcategories
    async function fetchSubcategories(page = 1) {
        currentPage = page;
        try {
            const url = `${API_URL}?page=${page}&search=${encodeURIComponent(searchQuery)}`;
            const response = await fetch(url);
            const data = await response.json();
            renderTable(data.data);
            renderPagination(data);
        } catch (error) {
            console.error("Error fetching subcategories:", error);
            document.getElementById('categoriesTableBody').innerHTML = `<tr><td colspan="5" class="text-danger text-center py-4">Failed to load subcategories.</td></tr>`;
        }
    }

    // Render Table
    function renderTable(categories) {
        const tbody = document.getElementById('categoriesTableBody');
        tbody.innerHTML = '';
        
        if(categories.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">No subcategories found.</td></tr>`;
            return;
        }

        categories.forEach(cat => {
            const imgUrl = cat.image ? `/${cat.image}` : 'https://placehold.co/50x50/f4f7f6/6b7280?text=Img';
            const statusBadge = cat.status 
                ? '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">Active</span>'
                : '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1 rounded-pill">Inactive</span>';
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><img src="${imgUrl}" class="category-img border" alt="${cat.name}"></td>
                <td class="text-muted">${cat.category ? cat.category.title : 'None'}</td>
                <td class="fw-semibold">${cat.name}</td>
                <td>${statusBadge}</td>
                <td class="text-end text-nowrap">
                    <button class="btn btn-sm btn-light text-brand me-1" onclick='openModal("edit", ${JSON.stringify(cat).replace(/'/g, "&#39;")})'>
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button class="btn btn-sm btn-light text-danger" onclick="deleteCategory('${cat.uuid}')">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // Pagination Render
    function renderPagination(data) {
        const container = document.getElementById('paginationLinks');
        const info = document.getElementById('pageInfo');
        container.innerHTML = '';
        
        info.innerText = `Showing ${data.from || 0} to ${data.to || 0} of ${data.total} entries`;
        
        if(data.links && data.links.length > 0) {
            data.links.forEach(link => {
                const activeClass = link.active ? 'active' : '';
                const disabledClass = link.url === null ? 'disabled' : '';
                let pageNum = '#';
                if(link.url) {
                    const urlObj = new URL(link.url);
                    pageNum = urlObj.searchParams.get('page');
                }
                
                const li = `
                    <li class="page-item ${activeClass} ${disabledClass}">
                        <button class="page-link" onclick="fetchSubcategories(${pageNum})" ${link.url === null ? 'disabled' : ''}>
                            ${link.label}
                        </button>
                    </li>
                `;
                container.insertAdjacentHTML('beforeend', li);
            });
        }
    }

    // Open Modal
    function openModal(mode, category = null) {
        const form = document.getElementById('categoryForm');
        form.reset();
        document.getElementById('currentImageContainer').style.display = 'none';
        
        if (mode === 'add') {
            document.getElementById('categoryOffcanvasLabel').innerText = 'Add Subcategory';
            document.getElementById('categoryId').value = '';
            document.getElementById('status').checked = true;
        } else {
            document.getElementById('categoryOffcanvasLabel').innerText = 'Edit Subcategory';
            document.getElementById('categoryId').value = category.uuid;
            document.getElementById('category_id').value = category.category_id;
            document.getElementById('title').value = category.name;
            document.getElementById('description').value = category.description || '';
            document.getElementById('status').checked = category.status == 1;
            
            if(category.image) {
                document.getElementById('currentImageContainer').style.display = 'block';
                document.getElementById('currentImage').src = `/${category.image}`;
            }
        }
        categoryOffcanvas.show();
    }

    // Handle Form Submit
    document.getElementById('categoryForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('categoryId').value;
        const formData = new FormData(this);
        
        // Fix checkbox boolean value for Laravel validation
        formData.set('status', document.getElementById('status').checked ? 1 : 0);
        
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
                categoryOffcanvas.hide();
                fetchSubcategories(currentPage);
            } else {
                Swal.fire('Error', data.message || 'Validation Error', 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'An unexpected error occurred.', 'error');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerText = 'Save Category';
        }
    });

    // Delete Category
    function deleteCategory(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
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
                        fetchSubcategories(currentPage);
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error!', 'Failed to delete subcategory.', 'error');
                }
            }
        })
    }
</script>
@endpush
