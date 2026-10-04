@extends('layouts.admin')

@section('title', 'FAQs')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/categories.css') }}">
    <!-- SweetAlert2 for notifications -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
@endpush

@section('content')
<h1 class="page-title mb-3">FAQs</h1>

<div class="d-flex flex-column flex-md-row gap-3 mb-4">
    <div class="input-group bg-white rounded" style="flex: 1; border: 1px solid var(--border-color);">
        <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
        <input type="text" id="searchInput" class="form-control border-0 ps-0 py-2" placeholder="Search by Question..." style="box-shadow: none;">
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
                    <th style="width: 5%">ID</th>
                    <th style="width: 25%">Question</th>
                    <th style="width: 45%">Answer</th>
                    <th style="width: 10%">Status</th>
                    <th class="text-end" style="width: 15%">Actions</th>
                </tr>
            </thead>
            <tbody id="faqsTableBody">
                <tr>
                    <td colspan="5" class="text-center py-4">Loading FAQs...</td>
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
<div class="offcanvas offcanvas-end" tabindex="-1" id="faqOffcanvas" aria-labelledby="faqOffcanvasLabel" style="width: 500px; max-width: 100vw;">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title" id="faqOffcanvasLabel">Add FAQ</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
      <form id="faqForm">
          <input type="hidden" id="faqId">
          
          <div class="mb-3">
              <label for="question" class="form-label">Question <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="question" name="question" required>
          </div>
          
          <div class="mb-3">
              <label for="answer" class="form-label">Answer <span class="text-danger">*</span></label>
              <textarea class="form-control" id="answer" name="answer" rows="6" required></textarea>
          </div>
          
          <div class="mb-3 form-check form-switch">
              <input class="form-check-input" type="checkbox" id="status" name="status" checked>
              <label class="form-check-label" for="status">Active</label>
          </div>
          
          <div class="mt-4">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="offcanvas">Cancel</button>
            <button type="submit" class="btn btn-primary" id="saveBtn">Save FAQ</button>
          </div>
      </form>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
<script>
    const API_URL = "{{ url('/api/faqs') }}";
    let currentPage = 1;
    let searchQuery = '';
    const faqOffcanvas = new bootstrap.Offcanvas(document.getElementById('faqOffcanvas'));
    
    document.addEventListener('DOMContentLoaded', () => fetchFaqs(1));

    let searchTimeout = null;
    document.getElementById('searchInput').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchQuery = e.target.value;
        searchTimeout = setTimeout(() => {
            fetchFaqs(1);
        }, 400);
    });

    async function fetchFaqs(page = 1) {
        currentPage = page;
        try {
            const url = `${API_URL}?page=${page}&search=${encodeURIComponent(searchQuery)}`;
            const response = await fetch(url);
            const data = await response.json();
            renderTable(data.data);
            renderPagination(data);
        } catch (error) {
            Swal.fire('Error', 'Failed to load FAQs', 'error');
        }
    }

    function renderTable(faqs) {
        const tbody = document.getElementById('faqsTableBody');
        tbody.innerHTML = '';
        
        if(faqs.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4">No FAQs found.</td></tr>`;
            return;
        }

        faqs.forEach(faq => {
            const statusBadge = `
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" onchange="toggleStatus('${faq.id}', this.checked)" ${faq.status ? 'checked' : ''}>
                </div>
            `;
                
            const row = `
                <tr>
                    <td>${faq.id}</td>
                    <td class="fw-semibold text-wrap" style="max-width: 250px;">${faq.question}</td>
                    <td class="text-wrap" style="max-width: 350px;">${faq.answer.substring(0, 100)}${faq.answer.length > 100 ? '...' : ''}</td>
                    <td>${statusBadge}</td>
                    <td class="text-end text-nowrap">
                        <button class="btn btn-sm btn-light text-brand me-1" onclick='openModal("edit", ${JSON.stringify(faq).replace(/'/g, "&#39;")})'>
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button class="btn btn-sm btn-light text-danger" onclick="deleteFaq('${faq.id}')">
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
                    <button class="page-link" onclick="fetchFaqs(${pageNum})" ${link.url === null ? 'disabled' : ''}>
                        ${link.label}
                    </button>
                </li>
            `;
            ul.insertAdjacentHTML('beforeend', li);
        });
    }

    function openModal(mode, faq = null) {
        const form = document.getElementById('faqForm');
        form.reset();
        
        if (mode === 'add') {
            document.getElementById('faqOffcanvasLabel').innerText = 'Add FAQ';
            document.getElementById('faqId').value = '';
            document.getElementById('status').checked = true;
        } else {
            document.getElementById('faqOffcanvasLabel').innerText = 'Edit FAQ';
            document.getElementById('faqId').value = faq.id;
            document.getElementById('question').value = faq.question;
            document.getElementById('answer').value = faq.answer;
            document.getElementById('status').checked = faq.status == 1;
        }
        faqOffcanvas.show();
    }

    document.getElementById('faqForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const id = document.getElementById('faqId').value;
        const formData = new FormData();
        
        formData.append('question', document.getElementById('question').value);
        formData.append('answer', document.getElementById('answer').value);
        formData.append('status', document.getElementById('status').checked ? 1 : 0);
        
        const isEdit = id !== '';
        const url = isEdit ? `${API_URL}/${id}` : API_URL;
        
        const saveBtn = document.getElementById('saveBtn');
        saveBtn.disabled = true;
        saveBtn.innerText = 'Saving...';

        try {
            const response = await fetch(url, {
                method: 'POST', // using POST since apiResource uses POST for create and we could use PUT but POST is easier for FormData in Laravel if we spoof _method, but since we're using simple API routes, let's just make sure routes/api.php or web.php handles this. Note: if using apiResource, update should be PUT/PATCH. I will ensure our routes use POST for update like banner does.
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            
            if(response.ok) {
                Swal.fire('Success', data.message, 'success');
                faqOffcanvas.hide();
                fetchFaqs(currentPage);
            } else {
                Swal.fire('Error', data.message || 'Validation Error', 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'An unexpected error occurred', 'error');
        } finally {
            saveBtn.disabled = false;
            saveBtn.innerText = 'Save FAQ';
        }
    });

    function deleteFaq(id) {
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
                        fetchFaqs(currentPage);
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error!', 'Failed to delete FAQ.', 'error');
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
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.message,
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                Swal.fire('Error', data.message || 'Error updating status', 'error');
                fetchFaqs(currentPage);
            }
        } catch (error) {
            Swal.fire('Error', 'An unexpected error occurred', 'error');
            fetchFaqs(currentPage);
        }
    }
</script>
@endpush
