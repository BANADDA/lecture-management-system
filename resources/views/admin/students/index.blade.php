@extends('layouts.app')

@section('content')
<x-navigation>
    <div class="dashboard-content">
        <!-- Page Header -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body d-flex justify-content-between align-items-center py-3">
                <div>
                    <h5 class="fw-bold mb-0">Students Management</h5>
                    <p class="text-muted small mb-0">Manage student records and enrollments</p>
                </div>
                <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Create New Student
                </a>
            </div>
        </div>

        <!-- Filters Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-filter me-2"></i>Filter Students</h6>
                <form method="GET" action="{{ route('admin.students.index') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Search by student ID or name" value="{{ request('search') }}">
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
                                    <i class="fas fa-calendar-alt text-muted"></i>
                                </span>
                                <select name="year" class="form-select border-start-0">
                                    <option value="">All Years</option>
                                    @for($i = 1; $i <= 6; $i++)
                                        <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>
                                            Year {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-filter me-2"></i>Apply Filters
                            </button>
                            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Students Table Card -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">#</th>
                                <th class="py-3">Student ID</th>
                                <th class="py-3">Name</th>
                                <th class="py-3">Program</th>
                                <th class="py-3">Year</th>
                                <th class="py-3">Semester</th>
                                <th class="py-3">Profile Photo</th>
                                <th class="py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($students as $student)
                                <tr class="student-row">
                                    <td class="ps-4">{{ $loop->iteration + (($students->currentPage()-1) * $students->perPage()) }}</td>
                                    <td><span class="badge bg-primary">{{ $student->student_id }}</span></td>
                                    <td class="fw-medium">{{ $student->full_name }}</td>
                                    <td>
                                        @if($student->program)
                                            <span class="d-inline-flex align-items-center">
                                                <i class="fas fa-graduation-cap me-2 text-muted"></i>
                                                {{ $student->program->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-info">Year {{ $student->current_year }}</span></td>
                                    <td><span class="badge bg-secondary">Semester {{ $student->current_semester }}</span></td>
                                    <td>
                                        @if($student->profile_photo)
                                            <img src="{{ asset('storage/' . $student->profile_photo) }}" alt="{{ $student->full_name }}" style="width:40px; height:40px; object-fit:cover;" class="rounded">
                                        @else
                                            <div style="width:40px; height:40px;" class="bg-light rounded d-flex align-items-center justify-content-center text-muted">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('admin.students.show', $student) }}" class="btn btn-sm btn-info text-white">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                            <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-sm btn-warning text-white">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger text-white" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $student->id }}">
                                                <i class="fas fa-trash me-1"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-user-graduate empty-state-icon"></i>
                                            <h6>No Students Found</h6>
                                            <p class="text-muted">No students match your current filters</p>
                                            <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-primary mt-2">Clear Filters</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($students->hasPages())
                <div class="card-footer bg-white d-flex justify-content-between py-3">
                    <div>
                        @if($students->total() > 0)
                            Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
                        @else
                            No students found
                        @endif
                    </div>
                    <div class="pagination-container">
                        {{ $students->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Delete Modals -->
        @foreach($students as $student)
            <div class="modal fade" id="deleteModal{{ $student->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $student->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel{{ $student->id }}">Confirm Delete</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete the student <strong>{{ $student->full_name }}</strong>?</p>
                            <p class="text-danger"><small>This action cannot be undone. All data associated with this student will be permanently deleted.</small></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form action="{{ route('admin.students.destroy', $student) }}" method="POST">
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
    /* Dashboard styling */
    .dashboard-content {
        padding: 1.5rem;
        background-color: #f8f9fa;
        min-height: 100vh;
    }

    /* Card styling */
    .card {
        border-radius: 0.5rem;
        overflow: hidden;
        border: none;
    }

    /* Table styling */
    .table {
        font-size: 0.875rem;
    }

    .table th {
        font-weight: 600;
    }

    /* Student row styling */
    .student-row {
        transition: background-color 0.15s ease-in-out;
    }

    .student-row:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.05);
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
    });
</script>
@endpush
@endsection
