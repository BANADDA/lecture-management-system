@extends('layouts.app')

@section('content')
<x-navigation>
    <div class="dashboard-content">
        <!-- Simple header instead of card -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-semibold mb-1">Student Details</h4>
                <p class="text-muted small mb-0">View complete student information</p>
            </div>
            <div>
                <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                    Back to Students
                </a>
                <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-warning btn-sm text-white">
                    Edit Student
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Profile section (left) -->
            <div class="col-lg-3 mb-4">
                <div class="profile-sidebar text-center pb-3">
                    {{-- @if($student->profile_photo)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $student->profile_photo) }}"
                                 alt="{{ $student->full_name }}"
                                 class="student-profile-image rounded-circle">
                        </div>
                    @else
                        <div class="mb-3">
                            <div class="student-profile-placeholder rounded-circle mx-auto">
                                <i class="fas fa-user fa-3x"></i>
                            </div>
                        </div>
                    @endif --}}

                    <h5 class="fw-bold mb-1">{{ $student->full_name }}</h5>
                    <div class="badge bg-primary mb-2">{{ $student->student_id }}</div>

                    <div class="mb-3">
                        <span class="status-badge {{ strtolower($student->status) === 'active' ? 'status-active' : 'status-inactive' }}">
                            {{ ucfirst($student->status) }}
                        </span>
                    </div>

                    <div class="contact-info mb-4">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="fas fa-phone text-primary me-2"></i>
                            <span>{{ $student->phone ?? 'Not provided' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content (right) -->
            <div class="col-lg-9">
                <!-- Academic Information Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Academic Information</h5>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="info-label">Program</label>
                                    <div class="fw-medium">
                                        {{ $student->program->name ?? 'Not assigned' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-4">
                                    <label class="info-label">Current Year</label>
                                    <div>
                                        <span class="badge bg-info rounded-pill px-3">Year {{ $student->current_year }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-4">
                                    <label class="info-label">Current Semester</label>
                                    <div>
                                        <span class="badge bg-secondary rounded-pill px-3">Semester {{ $student->current_semester }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enrolled Courses Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Enrolled Courses</h5>

                        @if(isset($student->courses) && $student->courses->count() > 0)
                            <div class="table-responsive mt-3">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Course Code</th>
                                            <th>Course Name</th>
                                            <th>Credits</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($student->courses as $course)
                                            <tr>
                                                <td><span class="badge bg-primary">{{ $course->code }}</span></td>
                                                <td>{{ $course->name }}</td>
                                                <td>{{ $course->credits }}</td>
                                                <td>
                                                    @php
                                                        $status = $course->pivot->status ?? 'enrolled';
                                                        $statusClass = [
                                                            'enrolled' => 'bg-success',
                                                            'completed' => 'bg-info',
                                                            'dropped' => 'bg-danger',
                                                            'pending' => 'bg-warning'
                                                        ][$status] ?? 'bg-secondary';
                                                    @endphp
                                                    <span class="badge {{ $statusClass }}">
                                                        {{ ucfirst($status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                This student is not enrolled in any courses.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Academic History Card -->
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Academic History</h5>

                        <div class="academic-timeline mt-3">
                            <div class="history-item">
                                <div class="history-date">Mar 2025</div>
                                <div class="history-content">
                                    <div class="history-title">Current Enrollment</div>
                                    <div class="history-text">Year {{ $student->current_year }}, Semester {{ $student->current_semester }}</div>
                                </div>
                            </div>

                            <div class="history-item">
                                <div class="history-date">Sep 2024</div>
                                <div class="history-content">
                                    <div class="history-title">Program Enrollment</div>
                                    <div class="history-text">Enrolled in {{ $student->program->name ?? 'current program' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong>{{ $student->full_name }}</strong>?</p>
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
</x-navigation>

@push('styles')
<style>
    /* Dashboard content styling */
    .dashboard-content {
        padding: 1.5rem;
        background-color: #fff;
        min-height: 100vh;
    }

    /* Card styling */
    .card {
        border-radius: 0.5rem;
        overflow: hidden;
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 1.5rem;
    }

    .card-title {
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 0;
    }

    /* Profile sidebar styling */
    .profile-sidebar {
        background-color: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 1.5rem;
    }

    /* Student profile image styling */
    .student-profile-image {
        width: 120px;
        height: 120px;
        object-fit: cover;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .student-profile-placeholder {
        width: 120px;
        height: 120px;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #adb5bd;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* Status badge styling */
    .status-badge {
        display: inline-block;
        padding: 0.35em 0.8em;
        font-size: 0.75em;
        font-weight: 500;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 10rem;
    }

    .status-active {
        background-color: #198754;
    }

    .status-inactive {
        background-color: #6c757d;
    }

    /* Info label styling */
    .info-label {
        color: #6c757d;
        font-size: 0.85rem;
        display: block;
        margin-bottom: 0.25rem;
    }

    /* Academic timeline styling */
    .academic-timeline {
        position: relative;
        padding-left: 1.5rem;
    }

    .history-item {
        position: relative;
        padding-bottom: 1.5rem;
        padding-left: 1rem;
        border-left: 2px solid #dee2e6;
    }

    .history-item:last-child {
        padding-bottom: 0;
    }

    .history-item:before {
        content: "";
        position: absolute;
        left: -0.5rem;
        top: 0;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        background-color: #0d6efd;
        border: 2px solid #fff;
    }

    .history-date {
        color: #6c757d;
        font-size: 0.85rem;
        margin-bottom: 0.25rem;
    }

    .history-title {
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
    }

    .history-text {
        color: #495057;
    }

    /* Button styling */
    .btn-sm {
        padding: 0.4rem 0.8rem;
    }

    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
    }

    .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
    }

    /* Table styling */
    .table {
        font-size: 0.9rem;
    }

    .table th {
        font-weight: 600;
        color: #495057;
    }

    .table-light th {
        background-color: #f8f9fa;
    }

    /* Remove unwanted elements */
    .navigation-arrow,
    .pagination-arrow,
    .chevron-navigation {
        display: none !important;
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
