<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Program;
use App\Models\Course;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Lecture;
use App\Models\LectureAttendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminDashboardController extends Controller
{
    /**
     * Display admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Basic stats
        $stats = [
            'students'          => Student::count(),
            'lecturers'         => Lecturer::count(),
            'courses'           => Course::count(),
            'departments'       => Department::count(),
            'faculties'         => Faculty::count(),
            'lectures_today'    => Lecture::whereDate('start_time', now()->toDateString())->count(),
            'lectures_completed'=> Lecture::where('is_completed', true)->count(),
            'lectures_upcoming' => Lecture::where('start_time', '>', now())->count(),
        ];

        // Get lectures by day of week (for a chart, for example)
        $lecturesByDay = DB::table('lectures')
            ->select(DB::raw('DAYNAME(start_time) as day, COUNT(*) as count'))
            ->groupBy('day')
            ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
            ->get();

        /*
         |-------------------------------------------------------
         | Fix for the Unknown Column 'courses.students_count'
         |-------------------------------------------------------
         | Instead of referencing a non-existent "students_count"
         | column in the 'courses' table, we do a subquery
         | to count how many students are enrolled in each course
         | via the student_courses pivot table.
         |
         | We also group by only the columns we SELECT (besides
         | aggregates), i.e. 'courses.id' and 'courses.code'.
         */

        // Get attendance statistics (for chart)
        $attendanceStats = DB::table('lectures')
            ->select(DB::raw("
                courses.code as course_code,
                COUNT(lecture_attendances.id) as attended,
                COUNT(DISTINCT lectures.id) *
                    (
                        SELECT COUNT(*)
                        FROM student_courses
                        WHERE student_courses.course_id = courses.id
                    )
                as expected
            "))
            ->join('courses', 'lectures.course_id', '=', 'courses.id')
            ->leftJoin('lecture_attendances', 'lectures.id', '=', 'lecture_attendances.lecture_id')
            ->where('lectures.is_completed', true)
            ->groupBy('courses.id', 'courses.code')
            ->orderBy('courses.code')
            ->limit(10)
            ->get();

        // Prepare attendance data for chart
        foreach ($attendanceStats as &$stat) {
            // The 'expected' might be 0 if no students are enrolled
            // or if no lectures exist. Avoid division by zero.
            $expected = (int) $stat->expected;
            $attended = (int) $stat->attended;
            $stat->percentage = $expected > 0
                ? round(($attended / $expected) * 100, 1)
                : 0;
        }

        // Get calendar events (lectures)
        $user = Auth::user();
        $lectures = [];

        // For admin, get all lectures
        if ($user->role === 'admin') {
            $lectures = Lecture::with(['course', 'lecturer'])
                ->whereDate('start_time', '>=', now()->subDays(30))
                ->whereDate('start_time', '<=', now()->addDays(60))
                ->get();
        }
        // For lecturer, only get their lectures (should not happen in this controller, but for safety)
        elseif ($user->role === 'lecturer' && $user->lecturer) {
            $lectures = Lecture::with(['course', 'lecturer'])
                ->where('lecturer_id', $user->lecturer->id)
                ->whereDate('start_time', '>=', now()->subDays(30))
                ->whereDate('start_time', '<=', now()->addDays(60))
                ->get();
        }

        // Format lectures for the calendar
        $calendarEvents = [];
        foreach ($lectures as $lecture) {
            // Set color based on lecture status
            $borderColor = '';
            if ($lecture->is_completed) {
                $borderColor = '#6c757d'; // gray for completed
            } elseif ($lecture->is_ongoing) {
                $borderColor = '#28a745'; // green for ongoing
            } else {
                $borderColor = '#007bff'; // blue for upcoming
            }

            // Calculate duration for display
            $duration = $lecture->start_time->diffInMinutes($lecture->end_time);
            $durationHours = floor($duration / 60);
            $durationMinutes = $duration % 60;
            $durationText = '';

            if ($durationHours > 0) {
                $durationText = $durationHours . ' hr' . ($durationHours > 1 ? 's' : '');
                if ($durationMinutes > 0) {
                    $durationText .= ' ' . $durationMinutes . ' min';
                }
            } else {
                $durationText = $durationMinutes . ' min';
            }

            // Get room number for display in the title
            $room = $lecture->room ?? 'TBA';

            // Ensure we have valid ISO 8601 formatted start and end times for proper rendering
            $startTimeString = $lecture->start_time->toIso8601String();
            $endTimeString = $lecture->end_time->toIso8601String();

            // Verify the end time is after start time (at least 15 minutes)
            if ($lecture->start_time->diffInMinutes($lecture->end_time) < 15) {
                // If less than 15 minutes, set minimum duration of 15 minutes
                $endTimeString = $lecture->start_time->copy()->addMinutes(15)->toIso8601String();
            }

            // Create formatted calendar event
            $calendarEvents[] = [
                'id' => $lecture->id,
                'title' => $lecture->course->code . ' - ' . $room,
                'start' => $startTimeString,
                'end' => $endTimeString,
                'borderColor' => $borderColor,
                'allDay' => false, // Explicitly set as not all-day event
                'editable' => false, // Prevent dragging/editing
                'description' => '<strong>Course:</strong> ' . $lecture->course->name .
                                 '<br><strong>Lecturer:</strong> ' . $lecture->lecturer->full_name .
                                 '<br><strong>Room:</strong> ' . $room .
                                 '<br><strong>Time:</strong> ' . $lecture->formatted_start_time . ' - ' . $lecture->formatted_end_time .
                                 '<br><strong>Duration:</strong> ' . $durationText .
                                 '<br><strong>Department:</strong> ' . ($lecture->course->department_name ?? 'N/A'),
                'extendedProps' => [
                    'course_id' => $lecture->course->id,
                    'course_name' => $lecture->course->name,
                    'lecturer_id' => $lecture->lecturer->id,
                    'lecturer_name' => $lecture->lecturer->full_name,
                    'department_id' => $lecture->course->department_id ?? 0,
                    'department_name' => $lecture->course->department_name ?? 'N/A',
                    'room' => $room,
                    'duration' => $durationText,
                    'duration_minutes' => $duration,
                    'is_completed' => $lecture->is_completed,
                    'is_ongoing' => $lecture->is_ongoing,
                    'attendance_percentage' => $lecture->attendance_percentage ?? 0
                ]
            ];
        }

        return view('admin.dashboard', compact('stats', 'lecturesByDay', 'attendanceStats', 'calendarEvents'));
    }

    /**
     * Display attendance report.
     *
     * @return \Illuminate\Http\Response
     */
    public function attendanceReport(Request $request)
    {
        $courses = Course::orderBy('name')->get();
        $selectedCourse = null;
        $attendanceData = null;

        if ($request->has('course_id')) {
            $selectedCourse = Course::findOrFail($request->course_id);
            $students = $selectedCourse->students; // many-to-many relationship
            $lectures = $selectedCourse->lectures()->where('is_completed', true)->get();

            $attendanceData = [];
            foreach ($students as $student) {
                $attendance = [];
                $totalAttended = 0;

                foreach ($lectures as $lecture) {
                    $hasAttended = $student->attendedLectures()
                        ->where('lecture_id', $lecture->id)
                        ->exists();

                    $attendance[] = [
                        'lecture_id' => $lecture->id,
                        'date'       => $lecture->formatted_date, // if you have a getFormattedDateAttribute
                        'attended'   => $hasAttended
                    ];

                    if ($hasAttended) {
                        $totalAttended++;
                    }
                }

                $percentage = $lectures->count() > 0
                    ? round(($totalAttended / $lectures->count()) * 100, 1)
                    : 0;

                $attendanceData[] = [
                    'student'         => $student,
                    'attendance'      => $attendance,
                    'total_attended'  => $totalAttended,
                    'total_lectures'  => $lectures->count(),
                    'percentage'      => $percentage
                ];
            }
        }

        return view('admin.reports.attendance', compact('courses', 'selectedCourse', 'attendanceData'));
    }

    /**
     * Display lecturers report.
     *
     * @return \Illuminate\Http\Response
     */
    public function lecturersReport(Request $request)
    {
        $departments = Department::orderBy('name')->get();
        $selectedDepartment = null;
        $lecturers = null;

        if ($request->has('department_id')) {
            $selectedDepartment = Department::findOrFail($request->department_id);
            $lecturers = $selectedDepartment->lecturers;

            // Get lecture statistics for each lecturer
            foreach ($lecturers as &$lecturer) {
                $lecturer->total_lectures     = $lecturer->lectures()->count();
                $lecturer->completed_lectures = $lecturer->lectures()->where('is_completed', true)->count();
                $lecturer->upcoming_lectures  = $lecturer->lectures()->where('start_time', '>', now())->count();

                // The number of distinct courses the lecturer teaches
                $lecturer->courses_count = $lecturer->lectureSchedules()
                    ->distinct('course_id')
                    ->count('course_id');
            }
        }

        return view('admin.reports.lecturers', compact('departments', 'selectedDepartment', 'lecturers'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'profile_photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo = $path;
        }

        // Update the database
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'profile_photo' => $user->profile_photo
            ]);

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check if current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        // Update password in the database
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'password' => Hash::make($request->password)
            ]);

        return redirect()->route('admin.profile')->with('success', 'Password updated successfully');
    }

    /**
     * Display students report.
     *
     * @return \Illuminate\Http\Response
     */
    public function studentsReport(Request $request)
    {
        $programs = Program::orderBy('name')->get();
        $selectedProgram = null;
        $students = null;

        if ($request->has('program_id')) {
            $selectedProgram = Program::findOrFail($request->program_id);
            $students = $selectedProgram->students;

            // Get course enrollment and attendance statistics for each student
            foreach ($students as &$student) {
                // Count how many courses are in 'enrolled' status
                $student->enrolled_courses = $student->courses()->wherePivot('status', 'enrolled')->count();
                // Count how many courses are in 'completed' status
                $student->completed_courses = $student->courses()->wherePivot('status', 'completed')->count();

                // Calculate overall attendance
                $totalLectures     = 0;
                $attendedLectures  = 0;

                foreach ($student->courses as $course) {
                    $courseLectures = $course->lectures()->where('is_completed', true)->count();
                    $totalLectures += $courseLectures;

                    $courseAttended = $student->attendedLectures()
                        ->where('course_id', $course->id)
                        ->where('is_completed', true)
                        ->count();

                    $attendedLectures += $courseAttended;
                }

                $student->attendance_percentage = $totalLectures > 0
                    ? round(($attendedLectures / $totalLectures) * 100, 1)
                    : 0;
            }
        }

        return view('admin.reports.students', compact('programs', 'selectedProgram', 'students'));
    }

    /**
     * Export timetable as PDF with specified range
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportTimetablePDF(Request $request)
    {
        // Get the range from the request
        $range = $request->query('range', 'week');

        // Set date ranges based on the selected option
        $startDate = now();
        $endDate = now();
        $title = '';

        switch ($range) {
            case 'day':
                $title = 'Daily Timetable: ' . $startDate->format('l, F j, Y');
                $endDate = $startDate->copy()->endOfDay();
                break;

            case 'week':
                $startDate = $startDate->startOfWeek();
                $endDate = $startDate->copy()->endOfWeek();
                $title = 'Weekly Timetable: ' . $startDate->format('M d') . ' - ' . $endDate->format('M d, Y');
                break;

            case 'month':
                $startDate = $startDate->startOfMonth();
                $endDate = $startDate->copy()->endOfMonth();
                $title = 'Monthly Timetable: ' . $startDate->format('F Y');
                break;

            case 'semester':
                // Determine current semester dates
                // This is an example - adjust based on your academic calendar
                $currentMonth = $startDate->month;

                if ($currentMonth >= 1 && $currentMonth <= 5) {
                    // Spring semester (January-May)
                    $startDate = Carbon::create($startDate->year, 1, 15);
                    $endDate = Carbon::create($startDate->year, 5, 15);
                    $title = 'Spring Semester Timetable: ' . $startDate->year;
                } elseif ($currentMonth >= 8 && $currentMonth <= 12) {
                    // Fall semester (August-December)
                    $startDate = Carbon::create($startDate->year, 8, 15);
                    $endDate = Carbon::create($startDate->year, 12, 15);
                    $title = 'Fall Semester Timetable: ' . $startDate->year;
                } else {
                    // Summer term
                    $startDate = Carbon::create($startDate->year, 6, 1);
                    $endDate = Carbon::create($startDate->year, 7, 31);
                    $title = 'Summer Term Timetable: ' . $startDate->year;
                }
                break;

            default:
                // Default to weekly view
                $startDate = $startDate->startOfWeek();
                $endDate = $startDate->copy()->endOfWeek();
                $title = 'Weekly Timetable: ' . $startDate->format('M d') . ' - ' . $endDate->format('M d, Y');
        }

        // Get lectures within the date range
        $lectures = Lecture::with(['course', 'lecturer'])
            ->whereBetween('start_time', [$startDate, $endDate])
            ->orderBy('start_time')
            ->get();

        // Group lectures by day for the timetable view
        $timetableData = [];
        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        // Initialize days
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dayName = strtolower($currentDate->format('l'));
            $formattedDate = $currentDate->format('M d');

            $timetableData[$dayName] = [
                'formatted_date' => $formattedDate,
                'date' => $currentDate->toDateString(),
                'lectures' => []
            ];

            $currentDate->addDay();
        }

        // Add lectures to appropriate days
        foreach ($lectures as $lecture) {
            $dayName = strtolower($lecture->start_time->format('l'));

            if (isset($timetableData[$dayName])) {
                $timetableData[$dayName]['lectures'][] = [
                    'id' => $lecture->id,
                    'course_code' => $lecture->course->code,
                    'course_name' => $lecture->course->name,
                    'room' => $lecture->room,
                    'lecturer' => $lecture->lecturer->full_name,
                    'start_time' => $lecture->start_time->format('h:i A'),
                    'end_time' => $lecture->end_time->format('h:i A'),
                    'duration' => $lecture->duration,
                    'is_completed' => $lecture->is_completed,
                    'attendance_percentage' => $lecture->attendance_percentage
                ];
            }
        }

        // Generate PDF
        $pdf = PDF::loadView('admin.exports.timetable-pdf', [
            'timetableData' => $timetableData,
            'title' => $title,
            'range' => $range
        ]);

        return $pdf->download($title . '.pdf');
    }
}
