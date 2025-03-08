@extends('layouts.app')

@section('content')
<x-navigation>
    <div class="dashboard-content">
        <h5 class="mb-2">Admin Dashboard</h5>

        {{-- SUMMARY STATS ROW - MOVED TO TOP --}}
        <div class="row g-3 mb-4 stats-overview">
            <div class="col-sm-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-primary-soft rounded-circle me-3">
                            <i class="fas fa-user-graduate text-primary"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small mb-0">Students</h6>
                            <h3 class="mb-0">{{ $stats['students'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-success-soft rounded-circle me-3">
                            <i class="fas fa-chalkboard-teacher text-success"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small mb-0">Lecturers</h6>
                            <h3 class="mb-0">{{ $stats['lecturers'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-warning-soft rounded-circle me-3">
                            <i class="fas fa-book text-warning"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small mb-0">Courses</h6>
                            <h3 class="mb-0">{{ $stats['courses'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-info-soft rounded-circle me-3">
                            <i class="fas fa-calendar-alt text-info"></i>
                        </div>
                        <div>
                            <h6 class="text-muted small mb-0">Today's Lectures</h6>
                            <h3 class="mb-0">{{ $stats['lectures_today'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CALENDAR SECTION - MADE MORE COMPACT --}}
        <div class="row">
            <div class="col-md-9">
                <div class="card shadow-sm mb-4 calendar-card">
                    <div class="card-header bg-white border-0 py-2">
                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <h5 class="fw-bold mb-0 me-3" id="calendar-title">Weekly Schedule</h5>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button id="prevBtn" class="btn btn-outline-secondary">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <button id="todayBtn" class="btn btn-outline-primary">Today</button>
                                    <button id="nextBtn" class="btn btn-outline-secondary">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                                <div class="btn-group btn-group-sm view-switch" role="group">
                                    <button type="button" class="btn btn-outline-primary active" data-view="timeGridWeek">Week</button>
                                    <button type="button" class="btn btn-outline-primary" data-view="dayGridMonth">Month</button>
                                    <button type="button" class="btn btn-outline-primary" data-view="timeGridDay">Day</button>
                                    <button type="button" class="btn btn-outline-primary" data-view="listWeek">List</button>
                                </div>
                                <select id="filterCourses" class="form-select form-select-sm" style="max-width: 150px;">
                                    <option value="all">All Courses</option>
                                    <!-- Will be populated via JavaScript -->
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>

            <!-- Details Panel - Right Side -->
            <div class="col-md-3">
                <div class="card shadow-sm h-100" id="details-panel">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">Lecture Details</h5>
                    </div>
                    <div class="card-body" id="lecture-details-content">
                        <div class="text-center py-5 text-muted" id="empty-details-placeholder">
                            <i class="fas fa-calendar-day fa-3x mb-3"></i>
                            <p>Select a lecture to view details</p>
                        </div>

                        <!-- Lecture details will be shown here -->
                        <div id="lecture-details" class="d-none">
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div id="detail-course-badge" class="course-badge me-2">CSC201</div>
                                    <div id="detail-duration-badge" class="badge bg-secondary">2 hrs</div>
                                </div>
                                <h5 id="detail-course-name" class="mb-1">Data Structures and Algorithms</h5>
                            </div>

                            <hr class="my-3">

                            <div class="mb-2">
                                <div class="d-flex mb-2">
                                    <div style="width: 30px;"><i class="fas fa-clock text-muted"></i></div>
                                    <div id="detail-time">12:15 PM - 02:15 PM</div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div style="width: 30px;"><i class="fas fa-calendar-alt text-muted"></i></div>
                                    <div id="detail-date">Monday, Mar 3, 2023</div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div style="width: 30px;"><i class="fas fa-door-open text-muted"></i></div>
                                    <div id="detail-room">101</div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div style="width: 30px;"><i class="fas fa-user-tie text-muted"></i></div>
                                    <div id="detail-lecturer">Eleanor Barnes</div>
                                </div>
                                <div class="d-flex mb-2">
                                    <div style="width: 30px;"><i class="fas fa-building text-muted"></i></div>
                                    <div id="detail-department">Computer Science</div>
                                </div>
                            </div>

                            <hr class="my-3">

                            <div class="d-flex mb-2 align-items-center">
                                <div style="width: 30px;"><i class="fas fa-chart-pie text-muted"></i></div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Attendance</span>
                                        <span id="detail-attendance-percentage">0%</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div id="detail-attendance-bar" class="progress-bar bg-info" role="progressbar" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3">

                            <div class="d-grid gap-2">
                                <a id="detail-view-link" href="#" class="btn btn-primary">
                                    <i class="fas fa-external-link-alt me-1"></i> View Full Details
                                </a>
                                <a id="detail-attendance-link" href="#" class="btn btn-outline-secondary">
                                    <i class="fas fa-clipboard-list me-1"></i> Manage Attendance
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- LECTURES BY DAY CHART & ATTENDANCE TABLE --}}
        <div class="row g-3 mb-4">
            {{-- LECTURES BY DAY (CHART) --}}
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0">Lectures by Day</h5>
                    </div>
                    <div class="card-body">
                        @if(isset($lecturesByDay) && $lecturesByDay->count())
                            <div class="chart-container" style="height: 250px;">
                                <canvas id="lecturesByDayChart"></canvas>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-chart-bar text-muted fa-3x mb-3"></i>
                                <p class="text-muted">No data available for lectures by day.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ATTENDANCE STATS (TABLE) --}}
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Attendance Overview</h5>
                        <a href="{{ route('admin.reports.attendance') }}" class="btn btn-sm btn-outline-primary">Full Report</a>
                    </div>
                    <div class="card-body p-0">
                        @if(isset($attendanceStats) && $attendanceStats->count())
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Course</th>
                                            <th class="text-center">Attended</th>
                                            <th class="text-center">Expected</th>
                                            <th class="text-center">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($attendanceStats as $stat)
                                        <tr>
                                            <td>{{ $stat->course_code }}</td>
                                            <td class="text-center">{{ $stat->attended }}</td>
                                            <td class="text-center">{{ $stat->expected }}</td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                        <div class="progress-bar bg-{{ $stat->percentage >= 75 ? 'success' : ($stat->percentage >= 50 ? 'warning' : 'danger') }}"
                                                            role="progressbar" style="width: {{ $stat->percentage }}%"
                                                            aria-valuenow="{{ $stat->percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <span class="text-muted small">{{ $stat->percentage }}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-clipboard-list text-muted fa-3x mb-3"></i>
                                <p class="text-muted">No attendance statistics available.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-navigation>
@endsection

@section('scripts')
<!-- FullCalendar CSS and JS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/js/all.min.js"></script>

<style>
    /* Dashboard styling */
    .dashboard-content {
        padding: 1.5rem;
        background-color: #f8f9fa;
    }

    /* Calendar styling - MORE COMPACT */
    .calendar-card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    #calendar {
        height: 450px;
        width: 100%;
        overflow: visible;
    }

    .fc-header-toolbar {
        display: none !important;
    }

    .fc .fc-col-header-cell-cushion {
        padding: 8px 5px; /* REDUCED PADDING */
        color: #444;
        font-weight: 600;
        font-size: 0.85rem; /* SMALLER FONT */
    }

    .fc-theme-standard td, .fc-theme-standard th {
        border-color: #e9ecef;
    }

    .fc-day-today {
        background-color: rgba(33, 150, 243, 0.05) !important;
    }

    /* Improve time slot display */
    .fc-timegrid-slot {
        height: 2.5em !important; /* REDUCED HEIGHT */
    }

    /* Fix scrolling within time grid - ENSURING SCROLLABILITY */
    .fc-scroller {
        overflow-y: auto !important; /* Force scrollbar to be visible */
        height: auto !important; /* Let the content dictate the height */
        -webkit-overflow-scrolling: touch; /* Add touchscreen support */
    }

    .fc-scroller-harness {
        overflow: visible !important;
    }

    .fc-view {
        overflow: visible !important;
    }

    /* Custom scrollbar styling */
    .dashboard-content::-webkit-scrollbar,
    .fc-scroller::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .dashboard-content::-webkit-scrollbar-track,
    .fc-scroller::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .dashboard-content::-webkit-scrollbar-thumb,
    .fc-scroller::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .dashboard-content::-webkit-scrollbar-thumb:hover,
    .fc-scroller::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Ensure timegrid is properly sized for scrolling */
    .fc-view-harness {
        overflow: visible !important;
        height: auto !important;
        min-height: 450px !important; /* MATCH CALENDAR HEIGHT */
    }

    /* Ensure the timegrid body can scroll */
    .fc-timegrid-body {
        overflow-y: auto !important;
        max-height: 390px !important; /* ADJUSTED TO MATCH NEW CALENDAR SIZE */
    }

    /* Fix time slots to ensure they're all visible with scrolling */
    .fc-timegrid-slots {
        min-height: 900px !important; /* REDUCED MIN HEIGHT */
    }

    /* Enhanced event styling for calendar events */
    .fc-event {
        border-radius: 4px;
        margin: 1px 0;
        padding: 4px;
        cursor: pointer;
        border-left-width: 6px !important;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* Remove all hover-related styles and replace with click indication */
    .fc-event:active {
        transform: scale(0.98);
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }

    /* Special styling for overlapping events - keep this but remove hover effects */
    .is-overlapping {
        background-image: repeating-linear-gradient(
            45deg,
            rgba(255, 255, 255, 0.15),
            rgba(255, 255, 255, 0.15) 5px,
            rgba(255, 255, 255, 0) 5px,
            rgba(255, 255, 255, 0) 10px
        ) !important;
        border-left-width: 6px !important;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
    }

    /* Remove the hover-expand functionality completely */
    /* Show a tooltip-like expanded view on hover for events */
    .hover-expand {
        display: none !important;
    }

    .hover-expand-content {
        display: none !important;
    }

    .hover-expand-header {
        display: none !important;
    }

    .hover-expand-course-badge {
        display: none !important;
    }

    .hover-expand-title {
        display: none !important;
    }

    .hover-expand-details {
        display: none !important;
    }

    .hover-expand-detail-row {
        display: none !important;
    }

    .hover-expand-detail-icon {
        display: none !important;
    }

    .hover-expand-content hr {
        display: none !important;
    }

    /* Course-specific colors - UPDATED with deeper, richer colors */
    .lecture-color-0 { background-color: #4A235A !important; border-left-color: #6C3483 !important; color: #fff !important; }  /* Deep Purple */
    .lecture-color-1 { background-color: #154360 !important; border-left-color: #1A5276 !important; color: #fff !important; }  /* Deep Blue */
    .lecture-color-2 { background-color: #7D6608 !important; border-left-color: #9A7D0A !important; color: #fff !important; }  /* Gold */
    .lecture-color-3 { background-color: #641E16 !important; border-left-color: #922B21 !important; color: #fff !important; }  /* Deep Red */
    .lecture-color-4 { background-color: #0E6251 !important; border-left-color: #117A65 !important; color: #fff !important; }  /* Deep Teal */
    .lecture-color-5 { background-color: #145A32 !important; border-left-color: #196F3D !important; color: #fff !important; }  /* Deep Green */
    .lecture-color-6 { background-color: #7E5109 !important; border-left-color: #9C640C !important; color: #fff !important; }  /* Brown */
    .lecture-color-7 { background-color: #512E5F !important; border-left-color: #76448A !important; color: #fff !important; }  /* Indigo */
    .lecture-color-8 { background-color: #1B4F72 !important; border-left-color: #21618C !important; color: #fff !important; }  /* Navy Blue */
    .lecture-color-9 { background-color: #186A3B !important; border-left-color: #1E8449 !important; color: #fff !important; }  /* Emerald */
    .lecture-color-10 { background-color: #7B241C !important; border-left-color: #A93226 !important; color: #fff !important; } /* Brick Red */
    .lecture-color-11 { background-color: #4A235A !important; border-left-color: #6C3483 !important; color: #fff !important; } /* Plum */
    .lecture-color-12 { background-color: #0B5345 !important; border-left-color: #0E6655 !important; color: #fff !important; } /* Pine */
    .lecture-color-13 { background-color: #7D6608 !important; border-left-color: #9A7D0A !important; color: #fff !important; } /* Olive */
    .lecture-color-14 { background-color: #641E16 !important; border-left-color: #922B21 !important; color: #fff !important; } /* Maroon */
    .lecture-color-15 { background-color: #1A5276 !important; border-left-color: #21618C !important; color: #fff !important; } /* Dark Blue */
    .lecture-color-16 { background-color: #0B5345 !important; border-left-color: #117864 !important; color: #fff !important; } /* Forest */
    .lecture-color-17 { background-color: #7E5109 !important; border-left-color: #9C640C !important; color: #fff !important; } /* Amber */
    .lecture-color-18 { background-color: #4A235A !important; border-left-color: #6C3483 !important; color: #fff !important; } /* Violet */
    .lecture-color-19 { background-color: #186A3B !important; border-left-color: #1E8449 !important; color: #fff !important; } /* Jungle */
    .lecture-color-20 { background-color: #1B2631 !important; border-left-color: #273746 !important; color: #fff !important; } /* Charcoal */
    .lecture-color-21 { background-color: #6E2C00 !important; border-left-color: #873600 !important; color: #fff !important; } /* Sienna */
    .lecture-color-22 { background-color: #424949 !important; border-left-color: #566573 !important; color: #fff !important; } /* Dark Gray */
    .lecture-color-23 { background-color: #7D3C98 !important; border-left-color: #8E44AD !important; color: #fff !important; } /* Amethyst */
    .lecture-color-24 { background-color: #1F618D !important; border-left-color: #2874A6 !important; color: #fff !important; } /* Steel Blue */
    .lecture-color-25 { background-color: #148F77 !important; border-left-color: #17A589 !important; color: #fff !important; } /* Jade */
    .lecture-color-26 { background-color: #B7950B !important; border-left-color: #D4AC0D !important; color: #fff !important; } /* Dark Yellow */
    .lecture-color-27 { background-color: #A04000 !important; border-left-color: #BA4A00 !important; color: #fff !important; } /* Rust */
    .lecture-color-28 { background-color: #633974 !important; border-left-color: #76448A !important; color: #fff !important; } /* Dark Purple */
    .lecture-color-29 { background-color: #1B4F72 !important; border-left-color: #21618C !important; color: #fff !important; } /* Deep Blue */
    .lecture-color-30 { background-color: #196F3D !important; border-left-color: #1E8449 !important; color: #fff !important; } /* Deep Green */
    .lecture-color-31 { background-color: #7B241C !important; border-left-color: #A93226 !important; color: #fff !important; } /* Crimson */

    /* Improved strip layout for events */
    .event-strip {
        display: flex;
        flex-direction: column; /* Changed to column for better layout */
        padding: 3px;
        height: 100%;
    }

    .event-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2px;
    }

    .event-time {
        font-weight: bold;
        font-size: 0.75rem;
        margin-right: 6px;
        white-space: nowrap;
    }

    .event-course {
        font-weight: bold;
        font-size: 0.85rem;
        margin-right: 6px;
    }

    .event-room {
        font-size: 0.7rem;
        border-radius: 3px;
        padding: 1px 4px;
        background-color: rgba(0,0,0,0.08);
        margin-left: auto;
        white-space: nowrap;
    }

    .event-lecturer {
        font-size: 0.75rem;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        opacity: 0.85;
        line-height: 1.1;
    }

    /* Status styling */
    .status-completed {
        opacity: 0.75;
        background-image: repeating-linear-gradient(
            45deg,
            rgba(0, 0, 0, 0.1),
            rgba(0, 0, 0, 0.1) 5px,
            rgba(0, 0, 0, 0) 5px,
            rgba(0, 0, 0, 0) 10px
        ) !important;
    }

    .status-ongoing {
        animation: pulse-border 2s infinite;
        border-color: #28a745 !important;
    }

    @keyframes pulse-border {
        0% { border-left-width: 5px; }
        50% { border-left-width: 10px; }
        100% { border-left-width: 5px; }
    }

    /* Custom stat card styling */
    .stat-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .bg-primary-soft {
        background-color: rgba(13, 110, 253, 0.15);
    }

    .bg-success-soft {
        background-color: rgba(25, 135, 84, 0.15);
    }

    .bg-warning-soft {
        background-color: rgba(255, 193, 7, 0.15);
    }

    .bg-info-soft {
        background-color: rgba(13, 202, 240, 0.15);
    }

    .stat-icon i {
        font-size: 22px;
    }

    /* Button group styling */
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
    }

    /* Card styling */
    .card {
        transition: box-shadow 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 6px 15px rgba(0,0,0,0.1) !important;
    }

    /* Custom scrollbar styling */
    .dashboard-content::-webkit-scrollbar,
    .fc-scroller::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .dashboard-content::-webkit-scrollbar-track,
    .fc-scroller::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .dashboard-content::-webkit-scrollbar-thumb,
    .fc-scroller::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .dashboard-content::-webkit-scrollbar-thumb:hover,
    .fc-scroller::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Custom course badge that stands out even in overlaps */
    .course-badge {
        background-color: rgba(0,0,0,0.3);
        border-radius: 4px;
        padding: 4px 8px;
        margin-bottom: 3px;
        display: inline-block;
        font-weight: bold;
        color: #fff;
        text-shadow: 0 1px 2px rgba(0,0,0,0.4);
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        letter-spacing: 0.5px;
        opacity: 1 !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    if (!calendarEl) {
        console.error('Calendar element not found!');
        return;
    }

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        allDaySlot: false,
        slotDuration: '00:30:00',
        slotLabelFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        },
        snapDuration: '00:15:00',
        headerToolbar: false,
        dayHeaderFormat: {
            weekday: 'short',
            month: 'short',
            day: 'numeric'
        },
        height: 'auto',
        contentHeight: 400,
        nowIndicator: true,
        navLinks: true,
        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5],
            startTime: '08:00',
            endTime: '18:00',
        },
        scrollTime: '08:00:00',
        slotMinTime: '08:00:00',
        slotMaxTime: '22:00:00',
        eventMinHeight: 0,
        eventMaxStack: 4, // Allow stacking for overlapping events
        eventDisplay: 'block',
        events: @json($calendarEvents ?? []),
        eventContent: function(arg) {
            let event = arg.event;

            // Format the time
            const startTime = event.start ? new Date(event.start).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: true}) : '';
            const endTime = event.end ? new Date(event.end).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: true}) : '';
            const timeText = `${startTime} - ${endTime}`;

            // Get course info
            const titleParts = event.title.split(' - ');
            const courseCode = titleParts[0];
            const room = titleParts.length > 1 ? titleParts[1] : '';

            // Get lecturer name
            let lecturerName = '';
            if (event.extendedProps && event.extendedProps.description) {
                const descriptionHTML = event.extendedProps.description;
                const lecturerMatch = descriptionHTML.match(/<strong>Lecturer:<\/strong> (.*?)<br>/);
                if (lecturerMatch && lecturerMatch[1]) {
                    lecturerName = lecturerMatch[1];
                }
            }

            // Create the strip layout
            const container = document.createElement('div');
            container.className = 'event-strip';

            // Header section with time and course code
            const headerEl = document.createElement('div');
            headerEl.className = 'event-header';

            // Time section
            const timeEl = document.createElement('span');
            timeEl.className = 'event-time';
            timeEl.textContent = startTime;
            headerEl.appendChild(timeEl);

            // Course code
            const courseEl = document.createElement('span');
            courseEl.className = 'event-course';
            courseEl.textContent = courseCode;
            headerEl.appendChild(courseEl);

            // Add header to container
            container.appendChild(headerEl);

            // ALWAYS display lecturer name
            const lecturerEl = document.createElement('div');
            lecturerEl.className = 'event-lecturer';
            lecturerEl.innerHTML = `<i class="fas fa-user-tie me-1" style="opacity: 0.7; font-size: 0.7rem;"></i>${lecturerName}`;
            container.appendChild(lecturerEl);

            // Room number (only show in larger events)
            if (event.end - event.start >= 45 * 60 * 1000) {
                const roomEl = document.createElement('span');
                roomEl.className = 'event-room';
                roomEl.textContent = room;
                container.appendChild(roomEl);
            }

            // Add a small attendance indicator for completed lectures
            if (event.extendedProps?.is_completed) {
                const attendanceEl = document.createElement('div');
                attendanceEl.style.fontSize = '0.65rem';
                attendanceEl.style.marginTop = '3px';
                attendanceEl.style.display = 'flex';
                attendanceEl.style.alignItems = 'center';

                const percentage = event.extendedProps.attendance_percentage || 0;
                const progressContainer = document.createElement('div');
                progressContainer.style.flex = '1';
                progressContainer.style.height = '3px';
                progressContainer.style.backgroundColor = 'rgba(0,0,0,0.1)';
                progressContainer.style.borderRadius = '2px';
                progressContainer.style.overflow = 'hidden';

                const progress = document.createElement('div');
                progress.style.height = '100%';
                progress.style.width = `${percentage}%`;
                progress.style.backgroundColor = percentage >= 75 ? 'rgba(40,167,69,0.5)' :
                                               percentage >= 50 ? 'rgba(255,193,7,0.5)' :
                                               'rgba(220,53,69,0.5)';

                progressContainer.appendChild(progress);
                attendanceEl.appendChild(progressContainer);

                container.appendChild(attendanceEl);
            }

            return { domNodes: [container] };
        },
        eventDidMount: function(info) {
            // Check for overlapping events
            const eventStart = info.event.start;
            const eventEnd = info.event.end;

            let isOverlapping = false;

            // Get all events in the same timeframe
            const allEvents = calendar.getEvents();
            allEvents.forEach(otherEvent => {
                if (otherEvent !== info.event &&
                    otherEvent.start < eventEnd &&
                    otherEvent.end > eventStart) {
                    isOverlapping = true;
                }
            });

            // Apply lecture-specific color class (using lecture ID instead of course ID)
            const lectureId = info.event.id || 0;
            const lectureColorClass = 'lecture-color-' + (lectureId % 32);
            info.el.classList.add(lectureColorClass);

            // Add a tooltip with full details
            $(info.el).tooltip({
                title: function() {
                    return info.event.extendedProps.description || '';
                },
                html: true,
                placement: 'top',
                container: 'body'
            });
        },
        eventClick: function(info) {
            // Add debugging
            console.log('Event clicked:', info.event);

            try {
                // Show lecture details in the side panel
                showLectureDetails(info.event);
            } catch (error) {
                console.error('Error in eventClick handler:', error);
                alert('Error showing lecture details. Check console for more information.');
            }
        },
        views: {
            timeGridWeek: {
                displayEventEnd: false,
                displayEventTime: false,
                dayHeaderFormat: { weekday: 'short', month: 'numeric', day: 'numeric' }
            },
            dayGridMonth: {
                displayEventEnd: false
            },
            timeGridDay: {
                displayEventTime: false
            },
            listWeek: {
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                }
            }
        }
    });

    calendar.render();

    function updateCalendarTitle() {
        let title = '';
        const view = calendar.view;

        if (view.type === 'timeGridWeek') {
            const start = calendar.view.activeStart;
            const end = calendar.view.activeEnd;
            const startMonth = start.toLocaleString('default', { month: 'short' });
            const endMonth = end.toLocaleString('default', { month: 'short' });

            const startDay = start.getDate();
            const endDay = new Date(end.getTime() - 86400000).getDate();

            if (startMonth === endMonth) {
                title = `${startMonth} ${startDay} - ${endDay}, ${start.getFullYear()}`;
            } else {
                title = `${startMonth} ${startDay} - ${endMonth} ${endDay}, ${start.getFullYear()}`;
            }
        } else if (view.type === 'dayGridMonth') {
            const date = calendar.getDate();
            title = date.toLocaleString('default', { month: 'long', year: 'numeric' });
        } else if (view.type === 'timeGridDay') {
            const date = calendar.getDate();
            title = date.toLocaleString('default', { weekday: 'long', month: 'short', day: 'numeric', year: 'numeric' });
        } else if (view.type === 'listWeek') {
            const start = calendar.view.activeStart;
            const end = calendar.view.activeEnd;
            const startMonth = start.toLocaleString('default', { month: 'short' });
            const endMonth = end.toLocaleString('default', { month: 'short' });

            const startDay = start.getDate();
            const endDay = new Date(end.getTime() - 86400000).getDate();

            title = `List View: ${startMonth} ${startDay} - ${endMonth} ${endDay}`;
        }

        document.getElementById('calendar-title').textContent = title;
    }

    updateCalendarTitle();

    let courses = new Set();
    let courseSelect = document.getElementById('filterCourses');
    const events = calendar.getEvents();

    events.forEach(event => {
        if (event.extendedProps && event.extendedProps.course_id) {
            const titleParts = event.title.split(' - ');
            const courseCode = titleParts[0];
            courses.add(courseCode);
        }
    });

    courses.forEach(course => {
        let option = document.createElement('option');
        option.value = course;
        option.textContent = course;
        courseSelect.appendChild(option);
    });

    document.getElementById('prevBtn').addEventListener('click', function() {
        calendar.prev();
        updateCalendarTitle();
    });

    document.getElementById('todayBtn').addEventListener('click', function() {
        calendar.today();
        updateCalendarTitle();
    });

    document.getElementById('nextBtn').addEventListener('click', function() {
        calendar.next();
        updateCalendarTitle();
    });

    document.querySelectorAll('.view-switch .btn').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.view-switch .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            this.classList.add('active');

            const view = this.getAttribute('data-view');
            calendar.changeView(view);
            updateCalendarTitle();
        });
    });

    document.getElementById('filterCourses').addEventListener('change', function() {
        const selectedCourse = this.value;

        if (selectedCourse === 'all') {
            calendar.getEvents().forEach(event => {
                event.setProp('display', 'auto');
            });
        } else {
            calendar.getEvents().forEach(event => {
                const titleParts = event.title.split(' - ');
                const courseCode = titleParts[0];

                if (courseCode === selectedCourse) {
                    event.setProp('display', 'auto');
                } else {
                    event.setProp('display', 'none');
                }
            });
        }
    });

    @if(isset($lecturesByDay) && $lecturesByDay->count())
    const ctx = document.getElementById('lecturesByDayChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($lecturesByDay->pluck('day')) !!},
            datasets: [{
                label: 'Number of Lectures',
                data: {!! json_encode($lecturesByDay->pluck('count')) !!},
                backgroundColor: [
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)',
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(201, 203, 207, 0.6)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(201, 203, 207, 1)'
                ],
                borderWidth: 1,
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    grid: {
                        display: true,
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    }
                }
            }
        }
    });
    @endif
});

// Function to display lecture details in the side panel
function showLectureDetails(event) {
    console.log('showLectureDetails called with event:', event);

    try {
        // Get all the data from the event
        const courseCode = event.title.split(' - ')[0];
        const room = event.extendedProps?.room || 'N/A';
        const courseName = event.extendedProps?.course_name || '';
        const lecturerName = event.extendedProps?.lecturer_name || 'N/A';
        const departmentName = event.extendedProps?.department_name || 'N/A';
        const durationText = event.extendedProps?.duration || '';
        const attendancePercentage = event.extendedProps?.attendance_percentage || 0;

        console.log('Extracted data:', { courseCode, room, courseName, lecturerName, departmentName });

        // Format date and time
        const startTime = event.start ? new Date(event.start).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: true}) : '';
        const endTime = event.end ? new Date(event.end).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: true}) : '';
        const timeText = `${startTime} - ${endTime}`;

        const dateOptions = { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' };
        const dateText = event.start ? new Date(event.start).toLocaleDateString('en-US', dateOptions) : '';

        // Extract data from description if needed
        let extracted = {
            courseName: courseName,
            lecturerName: lecturerName
        };

        if (event.extendedProps && event.extendedProps.description) {
            const descriptionHTML = event.extendedProps.description;

            // Extract course name if not already set
            if (!extracted.courseName) {
                const courseNameMatch = descriptionHTML.match(/<strong>Course:<\/strong> (.*?)<br>/);
                if (courseNameMatch && courseNameMatch[1]) {
                    extracted.courseName = courseNameMatch[1];
                }
            }

            // Extract lecturer name if not already set
            if (!extracted.lecturerName) {
                const lecturerMatch = descriptionHTML.match(/<strong>Lecturer:<\/strong> (.*?)<br>/);
                if (lecturerMatch && lecturerMatch[1]) {
                    extracted.lecturerName = lecturerMatch[1];
                }
            }
        }

        console.log('Final extracted data:', extracted);

        // Use lecture ID for color assignment to ensure unique colors per lecture
        const lectureId = event.id || 0;
        const colorIndex = lectureId % 32;
        // Array of background colors to match our course-color classes
        const bgColors = [
            '#4A235A', // Deep Purple
            '#154360', // Deep Blue
            '#7D6608', // Gold
            '#641E16', // Deep Red
            '#0E6251', // Deep Teal
            '#145A32', // Deep Green
            '#7E5109', // Brown
            '#512E5F', // Indigo
            '#1B4F72', // Navy Blue
            '#186A3B', // Emerald
            '#7B241C', // Brick Red
            '#4A235A', // Plum
            '#0B5345', // Pine
            '#7D6608', // Olive
            '#641E16', // Maroon
            '#1A5276', // Dark Blue
            '#0B5345', // Forest
            '#7E5109', // Amber
            '#4A235A', // Violet
            '#186A3B', // Jungle
            '#1B2631', // Charcoal
            '#6E2C00', // Sienna
            '#424949', // Dark Gray
            '#7D3C98', // Amethyst
            '#1F618D', // Steel Blue
            '#148F77', // Jade
            '#B7950B', // Dark Yellow
            '#A04000', // Rust
            '#633974', // Dark Purple
            '#1B4F72', // Deep Blue
            '#196F3D', // Deep Green
            '#7B241C', // Crimson
        ];
        const bgColor = bgColors[colorIndex];

        // Check if elements exist before updating
        const detailCourseBadge = document.getElementById('detail-course-badge');
        if (!detailCourseBadge) {
            console.error('Element detail-course-badge not found!');
            return;
        }

        console.log('Updating panel elements...');

        // Update the details panel
        detailCourseBadge.textContent = courseCode;
        detailCourseBadge.style.backgroundColor = bgColor;
        document.getElementById('detail-duration-badge').textContent = durationText;
        document.getElementById('detail-course-name').textContent = extracted.courseName;
        document.getElementById('detail-time').textContent = timeText;
        document.getElementById('detail-date').textContent = dateText;
        document.getElementById('detail-room').textContent = room;
        document.getElementById('detail-lecturer').textContent = extracted.lecturerName;
        document.getElementById('detail-department').textContent = departmentName;

        // Update attendance info
        document.getElementById('detail-attendance-percentage').textContent = `${attendancePercentage}%`;
        document.getElementById('detail-attendance-bar').style.width = `${attendancePercentage}%`;

        // Set the class for the attendance progress bar
        const attendanceBar = document.getElementById('detail-attendance-bar');
        if (attendancePercentage >= 75) {
            attendanceBar.className = 'progress-bar bg-success';
        } else if (attendancePercentage >= 50) {
            attendanceBar.className = 'progress-bar bg-warning';
        } else {
            attendanceBar.className = 'progress-bar bg-danger';
        }

        // Update the links
        document.getElementById('detail-view-link').href = `/admin/lectures/${event.id}`;
        document.getElementById('detail-attendance-link').href = `/admin/lectures/${event.id}/attendance`;

        // Show the details and hide the placeholder
        const emptyPlaceholder = document.getElementById('empty-details-placeholder');
        const lectureDetails = document.getElementById('lecture-details');

        if (!emptyPlaceholder || !lectureDetails) {
            console.error('Empty placeholder or lecture details element not found!');
            console.log('Elements found:', { emptyPlaceholder, lectureDetails });
            return;
        }

        emptyPlaceholder.classList.add('d-none');
        lectureDetails.classList.remove('d-none');

        console.log('Details panel updated successfully!');
    } catch (error) {
        console.error('Error in showLectureDetails:', error);
    }
}
</script>
@endsection
