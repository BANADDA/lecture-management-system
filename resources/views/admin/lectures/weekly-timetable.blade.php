@extends('layouts.app')

@section('content')
<x-navigation>
    <div class="dashboard-content">
        <!-- Page Header -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body d-flex justify-content-between align-items-center py-3">
                <div>
                    <h5 class="fw-bold mb-0">Weekly Timetable</h5>
                    <p class="text-muted small mb-0">View lecture schedules in a weekly format</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.lectures.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Lectures
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.export-timetable-pdf', ['date' => request('date')]) }}" target="_blank">
                                    <i class="fas fa-file-pdf me-2 text-danger"></i> Export as PDF
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.export-weekly-timetable', ['date' => request('date')]) }}">
                                    <i class="fas fa-file-excel me-2 text-success"></i> Export as Excel
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="window.print(); return false;">
                                    <i class="fas fa-print me-2 text-primary"></i> Print View
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Week Navigator -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0">{{ $weekInfo['start'] }} - {{ $weekInfo['end'] }}</h6>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.weekly-timetable', ['date' => \Carbon\Carbon::parse(request('date', now()))->subWeek()->format('Y-m-d')]) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-chevron-left me-1"></i> Previous Week
                    </a>
                    <a href="{{ route('admin.weekly-timetable') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-calendar-day me-1"></i> Current Week
                    </a>
                    <a href="{{ route('admin.weekly-timetable', ['date' => \Carbon\Carbon::parse(request('date', now()))->addWeek()->format('Y-m-d')]) }}" class="btn btn-outline-secondary btn-sm">
                        Next Week <i class="fas fa-chevron-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Weekly Timetable -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr class="bg-primary text-white">
                                @foreach($timetableData as $day => $data)
                                    <th class="text-center py-3 position-relative">
                                        <div class="day-name">{{ ucfirst($day) }}</div>
                                        <div class="day-date badge bg-light text-primary rounded-pill mt-1">{{ $data['formatted_date'] }}</div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach($timetableData as $day => $data)
                                    <td class="p-2 align-top" style="min-width: 200px; height: 400px;">
                                        @if(count($data['lectures']) > 0)
                                            @foreach($data['lectures'] as $lecture)
                                                <div class="lecture-card mb-3">
                                                    <div class="lecture-time d-flex justify-content-between align-items-center">
                                                        <span class="badge bg-primary text-white">{{ $lecture['start_time'] }} - {{ $lecture['end_time'] }}</span>
                                                        <span class="badge bg-secondary">{{ $lecture['duration'] }}</span>
                                                    </div>
                                                    <div class="lecture-course mt-2">
                                                        <span class="badge bg-info text-dark me-1">{{ $lecture['course_code'] }}</span>
                                                        <span class="fw-bold">{{ $lecture['course_name'] }}</span>
                                                    </div>
                                                    <div class="lecture-info mt-2 d-flex justify-content-between small">
                                                        <div>
                                                            <i class="fas fa-door-open text-muted me-1"></i> {{ $lecture['room'] }}
                                                        </div>
                                                        <div>
                                                            <i class="fas fa-user-tie text-muted me-1"></i> {{ $lecture['lecturer'] }}
                                                        </div>
                                                    </div>

                                                    @if(isset($lecture['attendance_percentage']))
                                                    <div class="mt-2">
                                                        <div class="d-flex justify-content-between small text-muted mb-1">
                                                            <span>Attendance</span>
                                                            <span>{{ round($lecture['attendance_percentage']) }}%</span>
                                                        </div>
                                                        <div class="progress" style="height: 5px;">
                                                            <div class="progress-bar bg-info" role="progressbar"
                                                                style="width: {{ $lecture['attendance_percentage'] }}%;"
                                                                aria-valuenow="{{ $lecture['attendance_percentage'] }}"
                                                                aria-valuemin="0"
                                                                aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="empty-state">
                                                <div class="text-center py-5">
                                                    <i class="fas fa-calendar-day empty-state-icon mb-3"></i>
                                                    <p class="text-muted">No lectures scheduled</p>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-navigation>
@endsection

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

    /* Day styling */
    .day-name {
        font-size: 1.1rem;
        font-weight: 600;
    }

    .day-date {
        font-size: 0.75rem;
    }

    /* Lecture card styling */
    .lecture-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        padding: 12px;
        border-left: 4px solid #0d6efd;
        transition: transform 0.15s ease;
    }

    .lecture-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .lecture-time {
        font-weight: 600;
    }

    .lecture-course {
        line-height: 1.3;
    }

    /* Empty state styling */
    .empty-state-icon {
        font-size: 2rem;
        color: #dee2e6;
    }

    /* Dropdown styling */
    .dropdown-item {
        display: flex;
        align-items: center;
        padding: 0.5rem 1rem;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    /* Print styles */
    @media print {
        .dashboard-content {
            padding: 0;
            background-color: white;
        }

        .card {
            box-shadow: none !important;
            margin-bottom: 0 !important;
        }

        .btn,
        .card-header .btn,
        .card-body .btn,
        .card-footer .btn,
        .dropdown {
            display: none !important;
        }

        .table, .table-responsive {
            border: 1px solid #dee2e6 !important;
        }

        .lecture-card {
            border: 1px solid #dee2e6 !important;
            box-shadow: none !important;
        }

        .badge {
            border: 1px solid #dee2e6 !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips and dropdowns
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });

        var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
        dropdownElementList.forEach(function (dropdownToggleEl) {
            new bootstrap.Dropdown(dropdownToggleEl);
        });
    });
</script>
@endpush
