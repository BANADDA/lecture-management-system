<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $monthName }} {{ $yearValue }} Timetable</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .month-title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0d6efd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .week-title {
            text-align: left;
            padding: 8px;
            font-size: 14px;
            font-weight: bold;
            background-color: #f0f0f0;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        th {
            background-color: #1a4377;
            color: white;
            padding: 8px;
            text-align: center;
            font-weight: normal;
            text-transform: uppercase;
        }
        td {
            border: 1px solid #ddd;
            padding: 6px;
            vertical-align: top;
            height: 150px;
        }
        .lecture {
            margin-bottom: 8px;
            padding: 5px;
            border-radius: 3px;
            background-color: #f8f9fa;
            border-left: 3px solid #0d6efd;
        }
        .lecture-time {
            font-weight: bold;
            color: #0d6efd;
        }
        .lecture-course {
            font-weight: bold;
        }
        .lecture-info {
            color: #666;
            font-size: 9px;
        }
        .attendance-bar {
            height: 4px;
            width: 100%;
            background-color: #e9ecef;
            margin-top: 3px;
        }
        .attendance-value {
            height: 100%;
            background-color: #0dcaf0;
        }
        .date-label {
            float: right;
            background-color: #f0f0f0;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        .no-lectures {
            color: #999;
            font-style: italic;
            text-align: center;
            padding-top: 60px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="month-title">{{ $monthName }} {{ $yearValue }} Lecture Timetable</div>
    </div>

    @foreach($weeksInMonth as $week)
        <div class="week-title">Week {{ $week }}</div>
        <table>
            <thead>
                <tr>
                    <th>Monday</th>
                    <th>Tuesday</th>
                    <th>Wednesday</th>
                    <th>Thursday</th>
                    <th>Friday</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day)
                        <td>
                            @if(isset($timetableData[$week][$day]) && count($timetableData[$week][$day]) > 0)
                                @foreach($timetableData[$week][$day] as $lecture)
                                    <div class="lecture">
                                        <div class="date-label">{{ \Carbon\Carbon::parse($lecture['date'])->format('d M') }}</div>
                                        <div class="lecture-time">{{ $lecture['start_time'] }} - {{ $lecture['end_time'] }}</div>
                                        <div class="lecture-course">{{ $lecture['course_code'] }}: {{ $lecture['course_name'] }}</div>
                                        <div class="lecture-info">
                                            <i>Room:</i> {{ $lecture['room'] }} | <i>Lecturer:</i> {{ $lecture['lecturer'] }}
                                        </div>
                                        @if(isset($lecture['attendance_percentage']))
                                            <div class="attendance-bar">
                                                <div class="attendance-value" style="width: {{ $lecture['attendance_percentage'] }}%;"></div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="no-lectures">No lectures</div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    @endforeach
</body>
</html>
