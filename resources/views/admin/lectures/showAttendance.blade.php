@extends('layouts.app')

@section('content')
<x-navigation>
    <div class="dashboard-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-semibold mb-1">Lecture Attendance</h4>
                <p class="text-muted small mb-0">{{ $lecture->course_name }} - {{ $lecture->formatted_date }}</p>
            </div>
            <div>
                <a href="{{ route('admin.lectures.show', $lecture) }}" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i> Back to Lecture
                </a>
                <a href="{{ route('admin.exportAttendance', $lecture) }}" class="btn btn-success">
                    <i class="fas fa-file-csv me-2"></i>Export Attendance
                </a>
            </div>
        </div>

        <!-- Attendance Summary Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="lecture-info">
                            <h5 class="card-title text-primary mb-3">Lecture Information</h5>
                            <div class="info-row">
                                <div class="info-label">Course</div>
                                <div class="info-value">
                                    <span class="badge bg-primary me-2">{{ $lecture->course_code }}</span>
                                    {{ $lecture->course_name }}
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Date & Time</div>
                                <div class="info-value">
                                    {{ $lecture->formatted_date }}, {{ $lecture->formatted_start_time }} - {{ $lecture->formatted_end_time }}
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Room</div>
                                <div class="info-value">{{ $lecture->room }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Lecturer</div>
                                <div class="info-value">{{ $lecture->lecturer_name }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="attendance-stats">
                            <h5 class="card-title text-primary mb-3">Attendance Statistics</h5>
                            <div class="row">
                                <div class="col-6">
                                    <div class="stat-card">
                                        <div class="stat-value">{{ $lecture->expected_students }}</div>
                                        <div class="stat-label">Expected</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-card">
                                        <div class="stat-value">{{ $lecture->actual_attendance }}</div>
                                        <div class="stat-label">Present</div>
                                    </div>
                                </div>
                            </div>
                            <div class="attendance-percentage-container mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span>Attendance Rate</span>
                                    <span class="fw-bold">{{ round($lecture->attendance_percentage) }}%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar {{ $lecture->attendance_percentage < 50 ? 'bg-danger' : ($lecture->attendance_percentage < 75 ? 'bg-warning' : 'bg-success') }}"
                                         role="progressbar"
                                         style="width: {{ $lecture->attendance_percentage }}%;"
                                         aria-valuenow="{{ $lecture->attendance_percentage }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance List Card -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title text-primary mb-0">Attendance Records</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" id="btnPrint">
                            <i class="fas fa-print me-1"></i> Print
                        </button>
                        <a href="{{ route('admin.lectures.exportAttendance', $lecture) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-download me-1"></i> Export
                        </a>
                    </div>
                </div>

                @if($lecture->attendees->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Check-in Time</th>
                                    <th>Method</th>
                                    <th>Comments</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lecture->attendees as $index => $student)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $student->student_id }}</td>
                                        <td>{{ $student->full_name }}</td>
                                        <td>
                                            @if($student->pivot->check_in_time)
                                                {{ \Carbon\Carbon::parse($student->pivot->check_in_time)->format('h:i A') }}
                                            @else
                                                -
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
                                                    'qr' => 'text-success',
                                                    'rfid' => 'text-primary',
                                                    'face' => 'text-info',
                                                    'manual' => 'text-secondary',
                                                ][$method] ?? 'text-secondary';
                                            @endphp
                                            <span class="d-inline-flex align-items-center {{ $methodClass }}">
                                                <i class="{{ $methodIcon }} me-2"></i>
                                                {{ ucfirst($method) }}
                                            </span>
                                        </td>
                                        <td>{{ $student->pivot->comment ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No attendance records found for this lecture.
                    </div>
                @endif
            </div>
        </div>

        <!-- Add Attendance Form (Only for completed lectures) -->
        @if($lecture->is_completed)
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body">
                    <h5 class="card-title text-primary mb-3">Manual Attendance Entry</h5>
                    <form id="addAttendanceForm" action="{{ route('admin.lectures.addAttendance', $lecture) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="student_id" class="form-label">Student ID</label>
                                    <input type="text" class="form-control" id="student_id" name="student_id" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="check_in_time" class="form-label">Check-in Time</label>
                                    <input type="time" class="form-control" id="check_in_time" name="check_in_time"
                                           value="{{ now()->format('H:i') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="check_in_method" class="form-label">Method</label>
                                    <select class="form-select" id="check_in_method" name="check_in_method">
                                        <option value="manual" selected>Manual</option>
                                        <option value="qr">QR Code</option>
                                        <option value="rfid">RFID Card</option>
                                        <option value="face">Face Recognition</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="comment" class="form-label">Comment (Optional)</label>
                                    <textarea class="form-control" id="comment" name="comment" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Add Attendance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
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
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .card-title {
        font-weight: 600;
        font-size: 1.1rem;
    }

    /* Info row styling */
    .info-row {
        display: flex;
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        width: 120px;
        font-weight: 500;
        color: #6c757d;
    }

    .info-value {
        flex: 1;
    }

    /* Stat card styling */
    .stat-card {
        text-align: center;
        padding: 1rem;
        background-color: #f0f0f0;
        border-radius: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #0d6efd;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #6c757d;
    }

    /* Progress bar styling */
    .progress {
        border-radius: 0.5rem;
        background-color: #e9ecef;
    }

    /* Table styling */
    .table {
        font-size: 0.875rem;
    }

    .table th {
        font-weight: 600;
        color: #495057;
    }

    /* Button styling */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 0.25rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* Form styling */
    .form-label {
        font-weight: 500;
        font-size: 0.875rem;
    }

    .form-control,
    .form-select {
        font-size: 0.875rem;
    }

    /* Print styles */
    @media print {
        .dashboard-content {
            padding: 0;
            background-color: white;
        }

        .card {
            box-shadow: none;
            border: none;
        }

        .btn,
        .nav,
        form,
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Print functionality
        const btnPrint = document.getElementById('btnPrint');
        if (btnPrint) {
            btnPrint.addEventListener('click', function() {
                window.print();
            });
        }

        // Form validation and submission for adding attendance
        const attendanceForm = document.getElementById('addAttendanceForm');
        if (attendanceForm) {
            attendanceForm.addEventListener('submit', function(event) {
                // Additional client-side validation could be added here

                // For now, we're just preventing the default form submission
                // and showing how you might implement AJAX submission
                event.preventDefault();

                const formData = new FormData(this);

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message and reload the page
                        alert('Attendance record added successfully!');
                        window.location.reload();
                    } else {
                        // Show error message
                        alert(data.message || 'Failed to add attendance record.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while processing your request.');
                });
            });
        }
    });
</script>
@endpush
@endsection
