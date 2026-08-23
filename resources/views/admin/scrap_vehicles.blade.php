@extends('layouts.admin')

@section('title', 'Scrap Vehicles - Scrap Daddy')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css">
    <style>
        .vehicle-img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">Scrap Vehicles</h1>
</div>

<div class="card shadow-sm border-0 rounded-3 mb-4">
    <div class="card-body">
        <form action="{{ route('admin.scrap-vehicles.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Search by Vehicle Number, Brand, Type or Customer..." value="{{ request('search') }}">
            </div>
            <select name="status" class="form-select w-auto">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
            <button type="submit" class="btn btn-primary px-4">Search</button>
            @if(request('search') || request('status'))
                <a href="{{ route('admin.scrap-vehicles.index') }}" class="btn btn-light px-3">Clear</a>
            @endif
        </form>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Customer</th>
                        <th>Vehicle Details</th>
                        <th>Remark</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $vehicle)
                    <tr>
                        <td class="ps-4 fw-semibold text-muted">#{{ $vehicle->id }}</td>
                        <td>
                            @if($vehicle->user)
                                <div class="fw-bold text-dark">{{ $vehicle->user->full_name }}</div>
                                <div class="text-muted small">{{ $vehicle->user->phone_number }}</div>
                            @else
                                <span class="text-muted">Unknown</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle align-self-start mb-1">
                                    {{ $vehicle->vehicle_type }}
                                </span>
                                <span class="fw-bold">{{ $vehicle->vehicle_number }}</span>
                                @if($vehicle->vehicle_brand || $vehicle->vehicle_model)
                                    <small class="text-muted">{{ $vehicle->vehicle_brand }} {{ $vehicle->vehicle_model }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="text-wrap d-inline-block" style="max-width: 200px;">
                                {{ $vehicle->remark ?: '-' }}
                            </span>
                        </td>
                        <td>
                            @if($vehicle->status == 'completed')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Completed</span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">Pending</span>
                            @endif
                        </td>
                        <td>
                            <div>{{ $vehicle->created_at->format('d M, Y') }}</div>
                            <small class="text-muted">{{ $vehicle->created_at->format('h:i A') }}</small>
                        </td>
                        <td class="pe-4">
                            <div class="d-flex gap-2">
                                @if($vehicle->photos && is_array($vehicle->photos) && count($vehicle->photos) > 0)
                                    <button class="btn btn-sm btn-outline-secondary view-photos-btn" data-photos="{{ json_encode($vehicle->photos) }}">
                                        <i class="fa-solid fa-image"></i> Photos
                                    </button>
                                @endif
                                
                                @if($vehicle->status != 'completed')
                                    <button class="btn btn-sm btn-success mark-completed-btn" data-id="{{ $vehicle->id }}">
                                        <i class="fa-solid fa-check"></i> Complete
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            No scrap vehicles found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-muted small fw-medium">
                Showing {{ $vehicles->firstItem() ?? 0 }} to {{ $vehicles->lastItem() ?? 0 }} of {{ $vehicles->total() }} entries
            </div>
            <div class="m-0">
                {{ $vehicles->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
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
        <div id="photosContainer" class="d-flex flex-wrap gap-2 justify-content-center">
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
    document.addEventListener('DOMContentLoaded', function() {
        const modal = new bootstrap.Modal(document.getElementById('vehicleModal'));
        const photosContainer = document.getElementById('photosContainer');

        // View Photos
        document.querySelectorAll('.view-photos-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const photos = JSON.parse(this.dataset.photos);
                photosContainer.innerHTML = '';
                
                if (photos.length > 0) {
                    photos.forEach(photo => {
                        const img = document.createElement('img');
                        img.src = '/' + photo;
                        img.className = 'img-fluid border rounded m-1';
                        img.style.maxHeight = '300px';
                        photosContainer.appendChild(img);
                    });
                } else {
                    photosContainer.innerHTML = '<p class="text-muted">No photos available.</p>';
                }
                
                modal.show();
            });
        });

        // Mark Completed
        document.querySelectorAll('.mark-completed-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Mark this vehicle as completed?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, complete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('/api/admin/scrap-vehicles/status', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ id: id, status: 'completed' })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 1) {
                                Swal.fire('Completed!', 'Vehicle marked as completed.', 'success')
                                .then(() => window.location.reload());
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            Swal.fire('Error', 'Failed to update status', 'error');
                        });
                    }
                });
            });
        });
    });
</script>
@endpush
