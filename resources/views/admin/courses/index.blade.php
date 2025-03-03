@extends('layouts.app')

@section('content')
<x-navigation>
    <div class="dashboard-content">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body d-flex justify-content-between align-items-center py-3">
                <div>
                    <h5 class="fw-bold mb-0">Courses Management</h5>
                    <p class="text-muted small mb-0">Manage your academic courses</p>
                </div>
                <a href="{{ route('admin.courses.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Add New Course
                </a>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-filter me-2"></i>Filter Courses</h6>
                <form method="GET" action="{{ route('admin.courses.index') }}">
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
                                    <i class="fas fa-graduation-cap text-muted"></i>
                                </span>
                                <select name="program_id" class="form-select border-start-0">
                                    <option value="">All Programs</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                            {{ $program->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-sort text-muted"></i>
                                </span>
                                <select name="sort" class="form-select border-start-0">
                                    <option value="">Sort By</option>
                                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                                    <option value="code_asc" {{ request('sort') == 'code_asc' ? 'selected' : '' }}>Code (A-Z)</option>
                                    <option value="code_desc" {{ request('sort') == 'code_desc' ? 'selected' : '' }}>Code (Z-A)</option>
                                    <option value="credits_asc" {{ request('sort') == 'credits_asc' ? 'selected' : '' }}>Credits (Low-High)</option>
                                    <option value="credits_desc" {{ request('sort') == 'credits_desc' ? 'selected' : '' }}>Credits (High-Low)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i>Apply
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary w-100">
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
                                <th class="ps-4 py-3">#</th>
                                <th class="py-3">Course Name</th>
                                <th class="py-3">Code</th>
                                <th class="py-3">Program</th>
                                <th class="py-3">Department</th>
                                <th class="py-3">Credits</th>
                                <th class="py-3">Description</th>
                                <th class="py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($courses as $course)
                                <tr class="course-row">
                                    <td class="ps-4">{{ $loop->iteration + (($courses->currentPage()-1) * $courses->perPage()) }}</td>
                                    <td class="fw-medium">{{ $course->name }}</td>
                                    <td><span class="badge bg-primary">{{ $course->code }}</span></td>
                                    <td>
                                        @if($course->program)
                                            <span class="d-inline-flex align-items-center">
                                                <i class="fas fa-graduation-cap me-2 text-muted"></i>
                                                {{ $course->program->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($course->department)
                                            <span class="d-inline-flex align-items-center">
                                                <i class="fas fa-building me-2 text-muted"></i>
                                                {{ $course->department->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $course->credits }} {{ Str::plural('credit', $course->credits) }}</span>
                                    </td>
                                    <td class="text-muted">{{ Str::limit($course->description, 50) }}</td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-sm btn-info text-white">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                            <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-sm btn-warning text-white">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger text-white" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $course->id }}">
                                                <i class="fas fa-trash me-1"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-book empty-state-icon"></i>
                                            <h6>No Courses Found</h6>
                                            <p class="text-muted">No courses match your current filters</p>
                                            <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-primary mt-2">Clear Filters</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if(method_exists($courses, 'links'))
                <div class="card-footer bg-white d-flex justify-content-between py-3">
                    <div>
                        @if($courses->total() > 0)
                            Showing {{ $courses->firstItem() }} to {{ $courses->lastItem() }} of {{ $courses->total() }} courses
                        @else
                            No courses found
                        @endif
                    </div>
                    <div class="pagination-container">
                        {{ $courses->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>

        <div class="pagination-container-external d-flex justify-content-center mb-5">
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <!-- Only show if there's a previous page -->
                    @if(method_exists($courses, 'currentPage') && $courses->currentPage() > 1)
                        <li class="page-item">
                            <a class="page-link" href="{{ $courses->url(1) }}" aria-label="First">
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="{{ $courses->previousPageUrl() }}" aria-label="Previous">
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
                    @if(method_exists($courses, 'lastPage'))
                        @php
                            $startPage = max(1, $courses->currentPage() - 2);
                            $endPage = min($courses->lastPage(), $courses->currentPage() + 2);
                        @endphp

                        @for($i = $startPage; $i <= $endPage; $i++)
                            <li class="page-item {{ $courses->currentPage() == $i ? 'active' : '' }}">
                                <a class="page-link" href="{{ $courses->url($i) }}">{{ $i }}</a>
                            </li>
                        @endfor
                    @endif

                    <!-- Only show if there's a next page -->
                    @if(method_exists($courses, 'hasMorePages') && $courses->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $courses->nextPageUrl() }}" aria-label="Next">
                                <i class="fas fa-angle-right"></i>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="{{ $courses->url($courses->lastPage()) }}" aria-label="Last">
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
        @foreach($courses as $course)
            <div class="modal fade" id="deleteModal{{ $course->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $course->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel{{ $course->id }}">Confirm Delete</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete the course "{{ $course->name }}"?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form action="{{ route('admin.courses.destroy', $course) }}" method="POST">
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
        border: none;
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

    /* Fix for button text not showing */
    .btn {
        text-align: center;
        justify-content: center;
        min-width: 68px; /* Ensure minimum width */
    }

    /* Badge styling */
    .badge {
        font-weight: 500;
        padding: 0.4em 0.6em;
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
    .course-row {
        transition: background-color 0.15s ease-in-out;
    }

    .course-row:hover {
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

        // Customize the pagination
        document.querySelectorAll('.pagination .page-link').forEach(link => {
            if (link.getAttribute('rel') === 'prev') {
                link.innerHTML = '<i class="fas fa-angle-left"></i>';
            } else if (link.getAttribute('rel') === 'next') {
                link.innerHTML = '<i class="fas fa-angle-right"></i>';
            }
        });
    });
</script>
@endpush
@endsection
