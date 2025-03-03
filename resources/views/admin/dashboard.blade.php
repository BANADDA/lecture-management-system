@extends('layouts.app')

@section('content')
<x-navigation>
    <div class="dashboard-content">
        <h5 class="mb-2">Admin Dashboard</h5>

        {{-- TOP STAT CARDS --}}
        <div class="row g-2 mb-2">
            <div class="col-6 col-md-3">
                <div class="card text-bg-primary">
                    <div class="card-body p-2">
                        <h6 class="card-title">Students</h6>
                        <p class="card-text fs-6">{{ $stats['students'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-bg-success">
                    <div class="card-body p-2">
                        <h6 class="card-title">Lecturers</h6>
                        <p class="card-text fs-6">{{ $stats['lecturers'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-bg-warning">
                    <div class="card-body p-2">
                        <h6 class="card-title">Courses</h6>
                        <p class="card-text fs-6">{{ $stats['courses'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card text-bg-danger">
                    <div class="card-body p-2">
                        <h6 class="card-title">Departments</h6>
                        <p class="card-text fs-6">{{ $stats['departments'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- EXTRA STAT CARDS --}}
        <div class="row g-2 mb-2">
            <div class="col-6 col-md-3">
                <div class="card bg-light border-0">
                    <div class="card-body p-2">
                        <h6 class="card-title">Faculties</h6>
                        <p class="card-text fs-6">{{ $stats['faculties'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card bg-light border-0">
                    <div class="card-body p-2">
                        <h6 class="card-title">Lectures Today</h6>
                        <p class="card-text fs-6">{{ $stats['lectures_today'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card bg-light border-0">
                    <div class="card-body p-2">
                        <h6 class="card-title">Completed Lectures</h6>
                        <p class="card-text fs-6">{{ $stats['lectures_completed'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card bg-light border-0">
                    <div class="card-body p-2">
                        <h6 class="card-title">Upcoming Lectures</h6>
                        <p class="card-text fs-6">{{ $stats['lectures_upcoming'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- LECTURES BY DAY CHART & ATTENDANCE TABLE --}}
        <div class="row g-2">
            {{-- LECTURES BY DAY (Chart) --}}
            <div class="col-md-6">
                <div class="card mb-2">
                    <div class="card-header bg-info text-white p-2">
                        <strong style="font-size: 0.9rem;">Lectures by Day</strong>
                    </div>
                    <div class="card-body p-2">
                        @if(isset($lecturesByDay) && $lecturesByDay->count())
                            <div class="chart-container">
                                <canvas id="lecturesByDayChart"></canvas>
                            </div>
                        @else
                            <p class="text-muted" style="font-size: 0.8rem;">No data available for lectures by day.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ATTENDANCE STATS (Table) --}}
            <div class="col-md-6">
                <div class="card mb-2">
                    <div class="card-header bg-secondary text-white p-2">
                        <strong style="font-size: 0.9rem;">Attendance Stats (Top 10 Courses)</strong>
                    </div>
                    <div class="card-body p-2">
                        @if(isset($attendanceStats) && $attendanceStats->count())
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle" style="font-size: 0.8rem;">
                                    <thead>
                                        <tr class="table-light">
                                            <th>Course</th>
                                            <th>Attended</th>
                                            <th>Expected</th>
                                            <th>%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($attendanceStats as $stat)
                                        <tr>
                                            <td>{{ $stat->course_code }}</td>
                                            <td>{{ $stat->attended }}</td>
                                            <td>{{ $stat->expected }}</td>
                                            <td>{{ $stat->percentage }}
                                                %</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted" style="font-size: 0.8rem;">No attendance statistics available.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-navigation>

    @endsection

    @section('scripts')
    @if(isset($lecturesByDay) && $lecturesByDay->count())
    <script>
        // Prepare labels and data from $lecturesByDay
        var lecturesByDayLabels = {!! json_encode($lecturesByDay->pluck('day')) !!};
        var lecturesByDayData = {!! json_encode($lecturesByDay->pluck('count')) !!};

        const ctx = document.getElementById('lecturesByDayChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: lecturesByDayLabels,
                datasets: [{
                    label: 'Lectures',
                    data: lecturesByDayData,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Lectures'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Day of the Week'
                        }
                    }
                }
            }
        });
    </script>
    @endif
    @endsection
