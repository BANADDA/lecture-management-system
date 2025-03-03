@extends('layouts.app')

@section('content')
<x-navigation>
    <div class="dashboard-content">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body d-flex justify-content-between align-items-center py-3">
                <div>
                    <h5 class="fw-bold mb-0">Departments Management</h5>
                    <p class="text-muted small mb-0">Manage your academic departments</p>
                </div>
                <a href="{{ route('admin.departments.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Add New Department
                </a>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-filter me-2"></i>Filter Departments</h6>
                <form method="GET" action="{{ route('admin.departments.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name or code" value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-map-marker-alt text-muted"></i>
                                </span>
                                <select name="campus" class="form-select border-start-0">
                                    <option value="">All Campuses</option>
                                    @php
                                        // Get unique campuses from departments collection
                                        $campusList = $departments->pluck('campus')->unique()->sort()->filter();
                                    @endphp
                                    @foreach($campusList as $campus)
                                        <option value="{{ $campus }}" {{ request('campus') == $campus ? 'selected' : '' }}>
                                            {{ $campus }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-university text-muted"></i>
                                </span>
                                <select name="faculty_id" class="form-select border-start-0">
                                    <option value="">All Faculties</option>
                                    @php
                                        // Get unique faculty IDs and names from departments
                                        $facultyList = $departments->map(function($dept) {
                                            return $dept->faculty ? ['id' => $dept->faculty->id, 'name' => $dept->faculty->name] : null;
                                        })->filter()->unique('id')->sortBy('name');
                                    @endphp
                                    @foreach($facultyList as $faculty)
                                        <option value="{{ $faculty['id'] }}" {{ request('faculty_id') == $faculty['id'] ? 'selected' : '' }}>
                                            {{ $faculty['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i>Apply
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 mb-5">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">Image</th>
                                <th class="py-3">Department Name</th>
                                <th class="py-3">Code</th>
                                <th class="py-3">Campus</th>
                                <th class="py-3">Faculty</th>
                                <th class="py-3">Description</th>
                                <th class="py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($departments as $department)
                                <tr class="department-row">
                                    <td class="ps-4">
                                        @if($department->image_url)
                                            <img src="{{ asset('storage/' . $department->image_url) }}"
                                                alt="{{ $department->name }}"
                                                style="width:50px; height:50px; object-fit:cover;"
                                                class="rounded">
                                        @else
                                            <div class="department-image-placeholder">
                                                <i class="fas fa-building"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="fw-medium">{{ $department->name }}</td>
                                    <td><span class="badge bg-primary">{{ $department->code }}</span></td>
                                    <td>
                                        <span class="d-inline-flex align-items-center">
                                            <i class="fas fa-map-marker-alt me-2 text-muted"></i>
                                            {{ $department->campus }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="d-inline-flex align-items-center">
                                            <i class="fas fa-university me-2 text-muted"></i>
                                            {{ $department->faculty->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ Str::limit($department->description, 80) }}</td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <a href="{{ route('admin.departments.show', $department) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $department->id }}">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-folder-open empty-state-icon"></i>
                                            <h6>No Departments Found</h6>
                                            <p class="text-muted">No departments match your current filters</p>
                                            <a href="{{ route('admin.departments.index') }}" class="btn btn-sm btn-primary mt-2">Clear Filters</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if(method_exists($departments, 'links'))
                <div class="card-footer bg-white d-flex justify-content-between py-3">
                    <div>
                        @if($departments->total() > 0)
                            Showing {{ $departments->firstItem() }} to {{ $departments->lastItem() }} of {{ $departments->total() }} departments
                        @else
                            No departments found
                        @endif
                    </div>
                    <div class="pagination-container">
                        {{ $departments->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>

        <div class="pagination-container-external d-flex justify-content-center mb-5">
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <!-- Only show if there's a previous page -->
                    @if(method_exists($departments, 'currentPage') && $departments->currentPage() > 1)
                        <li class="page-item">
                            <a class="page-link" href="{{ $departments->url(1) }}" aria-label="First">
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="{{ $departments->previousPageUrl() }}" aria-label="Previous">
                                <i class="fas fa-angle-left"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link"><i class="fas fa-angle-double-left"></i></span>
                        </li>
                        <li class="page-item disabled">
                            <span class="page-link"><i class="fas fa-angle-left"></i></span>
                        </li>
                    @endif

                    <!-- Page numbers -->
                    @if(method_exists($departments, 'lastPage'))
                        @php
                            $startPage = max(1, $departments->currentPage() - 2);
                            $endPage = min($departments->lastPage(), $departments->currentPage() + 2);
                        @endphp

                        @for($i = $startPage; $i <= $endPage; $i++)
                            <li class="page-item {{ $departments->currentPage() == $i ? 'active' : '' }}">
                                <a class="page-link" href="{{ $departments->url($i) }}">{{ $i }}</a>
                            </li>
                        @endfor
                    @endif

                    <!-- Only show if there's a next page -->
                    @if(method_exists($departments, 'hasMorePages') && $departments->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $departments->nextPageUrl() }}" aria-label="Next">
                                <i class="fas fa-angle-right"></i>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="{{ $departments->url($departments->lastPage()) }}" aria-label="Last">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link"><i class="fas fa-angle-right"></i></span>
                        </li>
                        <li class="page-item disabled">
                            <span class="page-link"><i class="fas fa-angle-double-right"></i></span>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>

        <!-- Delete Modals -->
        @foreach($departments as $department)
            <div class="modal fade" id="deleteModal{{ $department->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $department->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel{{ $department->id }}">Confirm Delete</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete the department "{{ $department->name }}"?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form action="{{ route('admin.departments.destroy', $department) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-navigation>

@push('styles')
<style>
    /* Table styling */
    .table {
        font-size: 0.875rem;
    }

    .table th {
        font-weight: 600;
    }

    /* Card styling */
    .card {
        border-radius: 0.5rem;
        overflow: hidden;
    }

    /* Action buttons */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        line-height: 1.5;
        border-radius: 0.2rem;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
    }

    .btn-sm i {
        margin-right: 3px;
    }

    .btn-info, .btn-warning, .btn-danger, .btn-primary {
        color: #fff !important;
    }

    /* Badge styling */
    .badge {
        font-weight: 500;
        padding: 0.4em 0.6em;
    }

    /* Department image placeholder */
    .department-image-placeholder {
        width: 50px;
        height: 50px;
        border-radius: 0.25rem;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #adb5bd;
        font-size: 1.2rem;
    }

    /* Empty state styling */
    .empty-state {
        padding: 2rem;
        text-align: center;
    }

    .empty-state-icon {
        font-size: 3rem;
        color: #dee2e6;
        margin-bottom: 1rem;
    }

    /* Dashboard content styling */
    .dashboard-content {
        padding: 1.5rem;
        background-color: #f8f9fa;
        min-height: 100vh;
    }

    /* Table row hover effect */
    .department-row {
        transition: background-color 0.15s ease-in-out;
    }

    .department-row:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.03);
    }

    /* Pagination styling */
    .pagination {
        margin-bottom: 0;
        display: flex;
        gap: 5px;
    }

    .page-item {
        margin: 0 2px;
    }

    .page-item .page-link {
        border-radius: 4px;
        padding: 0.375rem 0.75rem;
        color: var(--bs-primary);
        border: 1px solid #dee2e6;
    }

    .page-item.active .page-link {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
        color: white;
        font-weight: 500;
    }

    .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }

    .pagination-container {
        display: flex;
        align-items: center;
    }

    .pagination-container-external {
        margin-bottom: 2rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize any tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush
@endsection
