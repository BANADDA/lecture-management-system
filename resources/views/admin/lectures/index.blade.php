@extends('layouts.app')

@section('content')
<x-navigation>
    <div class="dashboard-content">
        <!-- Page Header -->
        <div class="card-body d-flex justify-content-between align-items-center py-3">
            <div>
                <h5 class="fw-bold mb-0">Lectures Management</h5>
                <p class="text-muted small mb-0">Manage and monitor course lectures</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.weekly-timetable') }}" class="btn btn-info btn-sm text-white">
                    <i class="fas fa-calendar-alt me-1"></i> Weekly Timetable
                </a>
                <a href="{{ route('admin.lectures.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Schedule New Lecture
                </a>
            </div>
        </div>

        <!-- Filters Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-filter me-2"></i>Filter Lectures</h6>
                <form method="GET" action="{{ route('admin.lectures.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Search lectures..." value="{{ $filters['search'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-book text-muted"></i>
                                </span>
                                <select name="course_id" class="form-select border-start-0">
                                    <option value="">All Courses</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ isset($filters['course_id']) && $filters['course_id'] == $course->id ? 'selected' : '' }}>
                                            {{ $course->code }} - {{ $course->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-user-tie text-muted"></i>
                                </span>
                                <select name="lecturer_id" class="form-select border-start-0">
                                    <option value="">All Lecturers</option>
                                    @foreach($lecturers as $lecturer)
                                        <option value="{{ $lecturer->id }}" {{ isset($filters['lecturer_id']) && $filters['lecturer_id'] == $lecturer->id ? 'selected' : '' }}>
                                            {{ $lecturer->full_name }}
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
                                <input type="date" name="date" class="form-control border-start-0" value="{{ $filters['date'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-filter me-2"></i>Apply
                            </button>
                            <a href="{{ route('admin.lectures.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Status Filter Tabs -->
                    <div class="mt-3">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link {{ ($filters['status'] ?? 'all') == 'all' ? 'active' : '' }}"
                                   href="{{ route('admin.lectures.index', array_merge(request()->except(['status', 'page']), ['status' => 'all'])) }}">
                                    All Lectures
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ ($filters['status'] ?? '') == 'upcoming' ? 'active' : '' }}"
                                   href="{{ route('admin.lectures.index', array_merge(request()->except(['status', 'page']), ['status' => 'upcoming'])) }}">
                                    Upcoming
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ ($filters['status'] ?? '') == 'today' ? 'active' : '' }}"
                                   href="{{ route('admin.lectures.index', array_merge(request()->except(['status', 'page']), ['status' => 'today'])) }}">
                                    Today
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ ($filters['status'] ?? '') == 'past' ? 'active' : '' }}"
                                   href="{{ route('admin.lectures.index', array_merge(request()->except(['status', 'page']), ['status' => 'past'])) }}">
                                    Past Lectures
                                </a>
                            </li>
                        </ul>
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

        <!-- Lectures Table -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">Course</th>
                                <th class="py-3">Date & Time</th>
                                <th class="py-3">Lecturer</th>
                                <th class="py-3">Room</th>
                                <th class="py-3">Status</th>
                                <th class="py-3">Attendance</th>
                                <th class="py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($lectures as $lecture)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            {{-- @if($lecture->image_url)
                                                <img src="{{ asset('storage/' . $lecture->image_url) }}"
                                                     alt="{{ $lecture->course_name }}"
                                                     class="lecture-thumbnail me-2">
                                            @else
                                                <div class="lecture-thumbnail-placeholder me-2">
                                                    <i class="fas fa-book"></i>
                                                </div>
                                            @endif --}}
                                            <div>
                                                <div class="fw-medium">{{ $lecture->course_name }}</div>
                                                <span class="badge bg-primary">{{ $lecture->course_code }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium mb-1">{{ $lecture->formatted_date }}</span>
                                            <span class="text-muted small">{{ $lecture->formatted_start_time }} - {{ $lecture->formatted_end_time }}</span>
                                            <span class="badge bg-secondary mt-1">{{ $lecture->duration }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-tie me-2 text-muted"></i>
                                            {{ $lecture->lecturer_name }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-door-open me-2 text-muted"></i>
                                            {{ $lecture->room }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($lecture->is_ongoing)
                                            <span class="badge bg-success">Ongoing</span>
                                        @elseif($lecture->is_completed)
                                            <span class="badge bg-secondary">Completed</span>
                                        @else
                                            <span class="badge bg-primary">Upcoming</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar bg-info" role="progressbar"
                                                     style="width: {{ $lecture->attendance_percentage }}%;"
                                                     aria-valuenow="{{ $lecture->attendance_percentage }}"
                                                     aria-valuemin="0"
                                                     aria-valuemax="100"></div>
                                            </div>
                                            <span class="ms-2 small">
                                                {{ $lecture->actual_attendance }}/{{ $lecture->expected_students }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('admin.lectures.show', $lecture) }}" class="btn btn-sm btn-info text-white">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                            <a href="{{ route('admin.lectures.edit', $lecture) }}" class="btn btn-sm btn-warning text-white">
                                                <i class="fas fa-edit me-1"></i> Edit
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger text-white" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $lecture->id }}">
                                                <i class="fas fa-trash me-1"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-chalkboard-teacher empty-state-icon"></i>
                                            <h6 class="mt-3">No Lectures Found</h6>
                                            <p class="text-muted">No lectures match your current filters</p>
                                            <a href="{{ route('admin.lectures.index') }}" class="btn btn-sm btn-primary mt-2">Clear Filters</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($lectures->hasPages())
                <div class="card-footer bg-white d-flex justify-content-between py-3">
                    <div>
                        Showing {{ $lectures->firstItem() }} to {{ $lectures->lastItem() }} of {{ $lectures->total() }} lectures
                    </div>
                    <div class="pagination-container">
                        {{ $lectures->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Delete Modals -->
        @foreach($lectures as $lecture)
            <div class="modal fade" id="deleteModal{{ $lecture->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $lecture->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel{{ $lecture->id }}">Confirm Delete</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete the lecture for <strong>{{ $lecture->course_name }}</strong> on <strong>{{ $lecture->formatted_date }}</strong>?</p>
                            <p class="text-danger"><small>This will also delete all attendance records associated with this lecture.</small></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form action="{{ route('admin.lectures.destroy', $lecture) }}" method="POST">
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
    /* Dashboard content styling */
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

    /* Lecture thumbnail styling */
    .lecture-thumbnail {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 6px;
    }

    .lecture-thumbnail-placeholder {
        width: 40px;
        height: 40px;
        background-color: #f0f0f0;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #adb5bd;
    }

    /* Table styling */
    .table {
        font-size: 0.875rem;
    }

    .table th {
        font-weight: 600;
    }

    /* Progress bar styling */
    .progress {
        border-radius: 5px;
        background-color: #e9ecef;
    }

    /* Nav tabs styling */
    .nav-tabs {
        border-bottom: 1px solid #dee2e6;
    }

    .nav-tabs .nav-link {
        margin-bottom: -1px;
        color: #495057;
        border: 1px solid transparent;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
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

    /* Empty state styling */
    .empty-state {
        padding: 2rem;
        text-align: center;
    }

    .empty-state-icon {
        font-size: 3rem;
        color: #dee2e6;
    }

    /* Pagination styling */
    .pagination {
        margin-bottom: 0;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush
@endsection
