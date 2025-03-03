<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>
        Mountains of Moon University Weekly Lecture Time Table
        <br>
        {{ $weekInfo['start'] }} - {{ $weekInfo['end'] }}
    </title>
    <style>
        /* Reset CSS */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #333;
        }

        /* Page header */
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 20px;
        }

        .title {
            font-size: 18pt;
            font-weight: bold;
            color: #0d6efd;
            margin-bottom: 10px;
        }

        .subtitle {
            font-size: 12pt;
            color: #6c757d;
        }

        /* Timetable grid */
        .timetable {
            width: 100%;
            border-collapse: collapse;
        }

        .timetable th {
            background-color: #1a4377;
            color: white;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #dee2e6;
        }

        .timetable th.time-header {
            width: 12%;
            background-color: #f8f9fa;
            color: #495057;
        }

        .timetable td {
            border: 1px solid #dee2e6;
            padding: 5px;
            vertical-align: top;
            height: 80px;
        }

        .timetable td.time-slot {
            background-color: #f8f9fa;
            text-align: center;
            font-weight: bold;
            color: #495057;
            font-size: 8pt;
        }

        /* Day header with date */
        .day-header {
            font-size: 10pt;
            font-weight: bold;
        }

        .day-date {
            font-size: 8pt;
            color: #6c757d;
            font-weight: normal;
        }

        /* Lecture styling */
        .lecture {
            background-color: #f8f9fa;
            border-left: 3px solid #0d6efd;
            padding: 5px;
            margin-bottom: 5px;
            border-radius: 3px;
            font-size: 8pt;
        }

        .lecture-time {
            font-weight: bold;
            color: #0d6efd;
            font-size: 8pt;
        }

        .lecture-course {
            font-weight: bold;
            margin: 3px 0;
        }

        .lecture-info {
            color: #6c757d;
            font-size: 7pt;
        }

        .no-lectures {
            color: #adb5bd;
            font-style: italic;
            text-align: center;
            padding-top: 30px;
            font-size: 8pt;
        }

        /* Footer */
        .footer {
            text-align: center;
            font-size: 8pt;
            color: #6c757d;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
        }

        /* Attendance bar */
        .attendance-container {
            width: 100%;
            background-color: #e9ecef;
            height: 5px;
            margin-top: 3px;
            border-radius: 2px;
            overflow: hidden;
        }

        .attendance-bar {
            height: 100%;
            background-color: #0dcaf0;
        }

        /* Page breaks */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">
            Mountains of Moon University
            <br>
            Weekly Lecture Time Table
        </div>
        <div class="subtitle">{{ $weekInfo['start'] }} - {{ $weekInfo['end'] }}</div>
    </div>

    <table class="timetable">
        <thead>
            <tr>
                <th class="time-header">Time</th>
                @foreach($days as $day)
                    <th>
                        <div class="day-header">
                            {{ ucfirst($day) }}
                            <div class="day-date">{{ $timetableData[$day]['formatted_date'] }}</div>
                        </div>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($timeSlots as $slotIndex => $slot)
                <tr>
                    <td class="time-slot">{{ $slot['label'] }}</td>

                    @foreach($days as $day)
                        <td>
                            @if(isset($timetableGrid[$slotIndex][$day]))
                                @php $lecture = $timetableGrid[$slotIndex][$day]; @endphp
                                <div class="lecture">
                                    <div class="lecture-time">{{ $lecture['start_time'] }} - {{ $lecture['end_time'] }}</div>
                                    <div class="lecture-course">{{ $lecture['course_code'] }}: {{ $lecture['course_name'] }}</div>
                                    <div class="lecture-info">
                                        Room: {{ $lecture['room'] }} | Lecturer: {{ $lecture['lecturer'] }}
                                    </div>
                                    @if(isset($lecture['attendance_percentage']))
                                        <div class="lecture-info">Attendance: {{ round($lecture['attendance_percentage']) }}%</div>
                                        <div class="attendance-container">
                                            <div class="attendance-bar" style="width: {{ $lecture['attendance_percentage'] }}%"></div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('F d, Y \a\t h:i A') }}</p>
    </div>
</body>
</html>
