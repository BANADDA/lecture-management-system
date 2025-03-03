@extends('layouts.app')

@section('content')
<x-navigation>
    <div class="dashboard-content">
        <!-- Header with Breadcrumb -->
        <div class="page-header mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.lectures.index') }}">Lectures</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Lecture Details</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Lecture Details</h4>
                    <p class="text-muted small mb-0">View complete lecture information and attendance</p>
                </div>
                <div class="action-buttons">
                    <a href="{{ route('admin.lectures.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                    <a href="{{ route('admin.lectures.edit', $lecture) }}" class="btn btn-warning btn-sm text-white">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Course Info (Left Column) -->
            <div class="col-lg-4">
                <!-- Course Details Card -->
                <div class="card shadow-sm border-0 mb-4 course-card">
                    <div class="card-header bg-primary text-white py-3">
                        <div class="d-flex align-items-center">
                            <div class="course-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="ms-3">
                                <h5 class="fw-bold mb-0">{{ $lecture->course_name }}</h5>
                                <span class="badge bg-white text-primary mt-1">{{ $lecture->course_code }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            @if($lecture->image_url)
                                <img src="{{ asset('storage/' . $lecture->image_url) }}"
                                     alt="{{ $lecture->course_name }}"
                                     class="lecture-image rounded">
                            @else
                                <div class="lecture-image-placeholder rounded-circle mx-auto">
                                    <i class="fas fa-chalkboard-teacher fa-2x"></i>
                                </div>
                            @endif
                        </div>

                        <div class="lecture-status-container text-center mb-4">
                            @if($lecture->is_ongoing)
                                <div class="lecture-status-badge status-ongoing pulse">
                                    <i class="fas fa-circle me-2"></i> Live Now
                                </div>
                            @elseif($lecture->is_completed)
                                <div class="lecture-status-badge status-completed">
                                    <i class="fas fa-check-circle me-2"></i> Completed
                                </div>
                            @else
                                <div class="lecture-status-badge status-upcoming">
                                    <i class="fas fa-clock me-2"></i> Upcoming
                                </div>
                            @endif
                        </div>

                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Department</div>
                                    <div class="info-value">{{ $lecture->department }}</div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Faculty</div>
                                    <div class="info-value">{{ $lecture->faculty }}</div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Lecturer</div>
                                    <div class="info-value">{{ $lecture->lecturer_name }}</div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="fas fa-door-open"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-label">Room</div>
                                    <div class="info-value">{{ $lecture->room }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="card shadow-sm border-0 actions-card">
                    <div class="card-header bg-light py-3">
                        <h6 class="fw-bold mb-0 text-primary"><i class="fas fa-cog me-2"></i>Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-3">
                            <a href="{{ route('admin.lectures.edit', $lecture) }}" class="btn btn-warning text-white action-btn">
                                <div class="btn-icon">
                                    <i class="fas fa-edit"></i>
                                </div>
                                <span>Edit Lecture</span>
                            </a>

                            <a href="{{ route('admin.lectures.attendance', $lecture) }}" class="btn btn-info text-white action-btn">
                                <div class="btn-icon">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <span>View Attendance</span>
                            </a>

                            @if(!$lecture->is_completed)
                                <form action="{{ route('admin.lectures.mark-completed', $lecture) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-primary w-100 action-btn">
                                        <div class="btn-icon">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <span>Mark as Completed</span>
                                    </button>
                                </form>
                            @endif

                            <button type="button" class="btn btn-danger action-btn" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <div class="btn-icon">
                                    <i class="fas fa-trash"></i>
                                </div>
                                <span>Delete Lecture</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lecture Details (Right Column) -->
            <div class="col-lg-8">
                <!-- Schedule Card -->
                <div class="card shadow-sm border-0 mb-4 schedule-card">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title mb-0"><i class="fas fa-calendar-alt text-primary me-2"></i>Schedule Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="schedule-info-card">
                                    <div class="schedule-icon">
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                    <div class="schedule-content">
                                        <div class="info-label">Date</div>
                                        <div class="info-value">{{ $lecture->formatted_date }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="schedule-info-card">
                                    <div class="schedule-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="schedule-content">
                                        <div class="info-label">Time</div>
                                        <div class="info-value">{{ $lecture->formatted_start_time }} - {{ $lecture->formatted_end_time }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="schedule-info-card">
                                    <div class="schedule-icon">
                                        <i class="fas fa-hourglass-half"></i>
                                    </div>
                                    <div class="schedule-content">
                                        <div class="info-label">Duration</div>
                                        <div class="info-value">{{ $lecture->duration }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Statistics Card -->
                <div class="card shadow-sm border-0 mb-4 attendance-card">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title mb-0"><i class="fas fa-chart-pie text-primary me-2"></i>Attendance Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="stat-value">{{ $lecture->expected_students }}</div>
                                    <div class="stat-label">Expected Students</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                    <div class="stat-value">{{ $lecture->actual_attendance }}</div>
                                    <div class="stat-label">Actual Attendance</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="fas fa-percentage"></i>
                                    </div>
                                    <div class="stat-value">{{ round($lecture->attendance_percentage) }}%</div>
                                    <div class="stat-label">Attendance Rate</div>
                                </div>
                            </div>
                        </div>

                        <div class="attendance-progress mb-2">
                            <div class="progress-label d-flex justify-content-between mb-1">
                                <span class="small">Attendance Progress</span>
                                <span class="small">{{ round($lecture->attendance_percentage) }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar {{ $lecture->attendance_percentage >= 75 ? 'bg-success' : ($lecture->attendance_percentage >= 50 ? 'bg-info' : 'bg-warning') }}"
                                     role="progressbar"
                                     style="width: {{ $lecture->attendance_percentage }}%;"
                                     aria-valuenow="{{ $lecture->attendance_percentage }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance List Card -->
                <div class="card shadow-sm border-0 attendance-list-card">
                    <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fas fa-list-check text-primary me-2"></i>Attendance List</h5>
                        <a href="{{ route('admin.lectures.export-attendance', $lecture) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-file-excel me-1"></i> Export Data
                        </a>
                    </div>
                    <div class="card-body">
                        @if($lecture->attendees->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover attendance-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Name</th>
                                            <th>Check-in Time</th>
                                            <th>Method</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lecture->attendees as $student)
                                            <tr>
                                                <td><code>{{ $student->student_id }}</code></td>
                                                <td>{{ $student->full_name }}</td>
                                                <td>
                                                    @if($student->pivot->check_in_time)
                                                        <span class="badge bg-light text-dark">
                                                            <i class="far fa-clock me-1"></i>
                                                            {{ \Carbon\Carbon::parse($student->pivot->check_in_time)->format('h:i A') }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light text-secondary">--:--</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $method = $student->pivot->check_in_method ?? 'manual';
                                                        $methodIcon = [
                                                            'qr' => 'fas fa-qrcode',
                                                            'rfid' => 'fas fa-id-card',
                                                            'face' => 'fas fa-user',
                                                            'manual' => 'fas fa-keyboard',
                                                        ][$method] ?? 'fas fa-keyboard';

                                                        $methodClass = [
                                                            'qr' => 'method-qr',
                                                            'rfid' => 'method-rfid',
                                                            'face' => 'method-face',
                                                            'manual' => 'method-manual',
                                                        ][$method] ?? 'method-manual';
                                                    @endphp
                                                    <span class="method-badge {{ $methodClass }}">
                                                        <i class="{{ $methodIcon }} me-1"></i>
                                                        {{ ucfirst($method) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <h6>No Attendance Records</h6>
                                <p class="text-muted">No students have checked in for this lecture yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="warning-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                    <p class="text-center mb-1">Are you sure you want to delete this lecture?</p>
                    <p class="text-danger text-center mb-0"><small>All attendance records associated with this lecture will be permanently deleted.</small></p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.lectures.destroy', $lecture) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Delete Permanently
                        </button>
                    </form>
                </div>
            </div>
        </div>
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

    /* Breadcrumb styling */
    .breadcrumb {
        padding: 0.5rem 0;
        background-color: transparent;
        margin-bottom: 0.5rem;
    }

    .breadcrumb-item a {
        color: #6c757d;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: #212529;
        font-weight: 500;
    }

    /* Page header styling */
    .page-header {
        margin-bottom: 1.5rem;
    }

    /* Card styling */
    .card {
        border-radius: 0.75rem;
        overflow: hidden;
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }

    .card-header {
        font-weight: 600;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .card-title {
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 0;
    }

    /* Course card styling */
    .course-card .card-header {
        padding: 1.25rem;
    }

    .course-icon {
        width: 48px;
        height: 48px;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    /* Lecture image styling */
    .lecture-image {
        width: 120px;
        height: 120px;
        object-fit: cover;
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        border: 4px solid white;
    }

    .lecture-image-placeholder {
        width: 120px;
        height: 120px;
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        border: 4px solid white;
        box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    }

    /* Lecture status badges */
    .lecture-status-badge {
        display: inline-block;
        padding: 0.5rem 1.25rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .status-ongoing {
        background-color: rgba(25, 135, 84, 0.15);
        color: #198754;
        position: relative;
    }

    .pulse::before {
        content: '';
        position: absolute;
        width: 8px;
        height: 8px;
        background-color: #198754;
        border-radius: 50%;
        top: 50%;
        left: 1rem;
        transform: translateY(-50%);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: translateY(-50%) scale(0.95);
            box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.5);
        }
        70% {
            transform: translateY(-50%) scale(1);
            box-shadow: 0 0 0 10px rgba(25, 135, 84, 0);
        }
        100% {
            transform: translateY(-50%) scale(0.95);
            box-shadow: 0 0 0 0 rgba(25, 135, 84, 0);
        }
    }

    .status-completed {
        background-color: rgba(108, 117, 125, 0.15);
        color: #6c757d;
    }

    .status-upcoming {
        background-color: rgba(13, 110, 253, 0.15);
        color: #0d6efd;
    }

    /* Info grid styling */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        transition: background-color 0.15s ease;
    }

    .info-item:hover {
        background-color: #e9ecef;
    }

    .info-icon {
        width: 40px;
        height: 40px;
        background-color: rgba(13, 110, 253, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0d6efd;
        margin-right: 1rem;
    }

    .info-content {
        flex: 1;
    }

    .info-label {
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-size: 0.875rem;
        font-weight: 500;
    }

    /* Actions card styling */
    .actions-card {
        background-color: white;
    }

    .action-btn {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        transition: transform 0.15s ease;
    }

    .action-btn:hover {
        transform: translateY(-2px);
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
    }

    /* Schedule card styling */
    .schedule-info-card {
        display: flex;
        align-items: center;
        padding: 1rem;
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        height: 100%;
        transition: background-color 0.15s ease;
    }

    .schedule-info-card:hover {
        background-color: #e9ecef;
    }

    .schedule-icon {
        width: 40px;
        height: 40px;
        background-color: rgba(13, 110, 253, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0d6efd;
        margin-right: 1rem;
    }

    .schedule-content {
        flex: 1;
    }

    /* Attendance Statistics styling */
    .stat-card {
        text-align: center;
        padding: 1.25rem;
        background-color: #f8f9fa;
        border-radius: 0.5rem;
        height: 100%;
        transition: transform 0.15s ease, background-color 0.15s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        background-color: #e9ecef;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        background-color: rgba(13, 110, 253, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0d6efd;
        margin: 0 auto 0.75rem;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.75rem;
        font-weight: 500;
        color: #6c757d;
    }

    /* Progress bar styling */
    .progress {
        height: 8px;
        border-radius: 1rem;
        background-color: #e9ecef;
        overflow: hidden;
    }

    .progress-bar {
        border-radius: 1rem;
        transition: width 0.8s ease;
    }

    /* Attendance List styling */
    .attendance-table {
        font-size: 0.875rem;
    }

    .attendance-table thead {
        background-color: #f8f9fa;
    }

    .attendance-table th {
        font-weight: 600;
        color: #495057;
        padding: 0.75rem 1rem;
    }

    .attendance-table td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
    }

    .attendance-table tbody tr {
        transition: background-color 0.15s ease;
    }

    .attendance-table tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }

    /* Method badges */
    .method-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .method-qr {
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754;
    }

    .method-rfid {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }

    .method-face {
        background-color: rgba(111, 66, 193, 0.1);
        color: #6f42c1;
    }

    .method-manual {
        background-color: rgba(108, 117, 125, 0.1);
        color: #6c757d;
    }

    /* Empty state styling */
    .empty-state {
        text-align: center;
        padding: 3rem 0;
    }

    .empty-icon {
        width: 64px;
        height: 64px;
        background-color: rgba(108, 117, 125, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        font-size: 1.5rem;
        margin: 0 auto 1rem;
    }

    /* Delete modal styling */
    .warning-icon {
        width: 80px;
        height: 80px;
        background-color: rgba(220, 53, 69, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #dc3545;
        font-size: 2rem;
        margin: 0 auto;
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {
        .info-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 576px) {
        .info-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
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

        // Animate progress bars on load
        const progressBars = document.querySelectorAll('.progress-bar');
        setTimeout(() => {
            progressBars.forEach(bar => {
                const value = bar.getAttribute('aria-valuenow');
                bar.style.width = value + '%';
            });
        }, 200);
    });
</script>
@endpush
@endsection
