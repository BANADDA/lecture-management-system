@extends('layouts.app')

@section('content')
<x-navigation>
    <div class="dashboard-content">
        <!-- Elegant Header with Create Button -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body d-flex justify-content-between align-items-center py-3">
                <div>
                    <h5 class="fw-bold mb-0">Programs Management</h5>
                    <p class="text-muted small mb-0">Manage your academic programs efficiently</p>
                </div>
                <a href="{{ route('admin.programs.create') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                    <i class="fas fa-plus me-2"></i> Create New Program
                </a>
            </div>
        </div>

        <!-- Stylish Filter Form -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-filter me-2"></i>Filter Programs</h6>
                <form method="GET" action="{{ route('admin.programs.index') }}">
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
                                    <i class="fas fa-university text-muted"></i>
                                </span>
                                <select name="department_id" class="form-select border-start-0">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
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
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i>Apply
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('admin.programs.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Beautiful Programs Table -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">#</th>
                                <th class="py-3">Program Name</th>
                                <th class="py-3">Code</th>
                                <th class="py-3">Department</th>
                                <th class="py-3">Duration</th>
                                <th class="py-3">Description</th>
                                <th class="py-3">Image</th>
                                <th class="py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($programs as $program)
                                <tr class="program-row">
                                    <td class="ps-4">{{ $loop->iteration + (($programs->currentPage()-1) * $programs->perPage()) }}</td>
                                    <td class="fw-medium">{{ $program->name }}</td>
                                    <td><span class="badge bg-primary">{{ $program->code }}</span></td>
                                    <td>
                                        @if($program->department)
                                            <span class="d-inline-flex align-items-center">
                                                <i class="fas fa-university me-2 text-muted"></i>
                                                {{ $program->department->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="d-inline-flex align-items-center">
                                            <i class="fas fa-calendar-alt me-2 text-muted"></i>
                                            {{ $program->duration_years }} yr{{ $program->duration_years > 1 ? 's' : '' }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ Str::limit($program->description, 50) }}</td>
                                    <td>
                                        @if($program->image_url)
                                            <img src="{{ asset('storage/' . $program->image_url) }}" alt="{{ $program->name }}" style="width:50px; height:50px; object-fit:cover;" class="rounded">
                                        @else
                                            <span class="badge bg-light text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <a href="{{ route('admin.programs.show', $program) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('admin.programs.edit', $program) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.programs.destroy', $program) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete {{ $program->name }}?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-folder-open empty-state-icon"></i>
                                            <h6>No Programs Found</h6>
                                            <p class="text-muted">No programs match your current filters</p>
                                            <a href="{{ route('admin.programs.index') }}" class="btn btn-sm btn-primary mt-2">Clear Filters</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($programs->hasPages())
                <div class="card-footer bg-white d-flex justify-content-between py-3">
                    <div>Showing {{ $programs->firstItem() }} to {{ $programs->lastItem() }} of {{ $programs->total() }} results</div>
                    <div class="pagination-container">
                        {{ $programs->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-navigation>
@endsection

@push('styles')
<style>
    /* Card styling */
    .card {
        border-radius: 0.5rem;
        overflow: hidden;
        transition: all 0.2s ease;
    }

    /* Table row styling */
    .program-row {
        transition: background-color 0.15s ease-in-out;
    }

    .program-row:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.03);
    }

    /* Button group styling */
    .btn-group .btn {
        border-radius: 0;
    }

    .btn-group .btn:first-child {
        border-top-left-radius: 0.25rem;
        border-bottom-left-radius: 0.25rem;
    }

    .btn-group .btn:last-child {
        border-top-right-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
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

    /* Badge styling */
    .badge {
        font-weight: 500;
        padding: 0.4em 0.6em;
    }

    /* Input group styling */
    .input-group-text {
        background-color: #f8f9fa;
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

    /* Add some shadow to the dashboard content */
    .dashboard-content {
        padding: 1.5rem;
        background-color: #f8f9fa;
        min-height: 100vh;
    }

    /* Fix button styling */
    .btn-primary, .btn-warning, .btn-danger {
        color: #fff;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }

    /* Footer styling */
    .card-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        font-size: 0.875rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Customize the pagination
        document.querySelectorAll('.pagination .page-link').forEach(link => {
            if (link.getAttribute('rel') === 'prev') {
                link.innerHTML = '<i class="fas fa-chevron-left"></i>';
            } else if (link.getAttribute('rel') === 'next') {
                link.innerHTML = '<i class="fas fa-chevron-right"></i>';
            }
        });
    });
</script>
@endpush
