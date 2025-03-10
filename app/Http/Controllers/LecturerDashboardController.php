<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Department;
use App\Models\Lecture;
use App\Models\LectureSchedule;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;

class LecturerDashboardController extends Controller
{
    /**
     * Display lecturer dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lecturer = Auth::user()->lecturer;

        // Get upcoming lectures
        $upcomingLectures = $lecturer->lectures()
            ->where('start_time', '>', now())
            ->where('is_completed', false)
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        // Get today's lectures
        $todayLectures = $lecturer->lectures()
            ->whereDate('start_time', now()->toDateString())
            ->orderBy('start_time')
            ->get();

        // Get recent lectures
        $recentLectures = $lecturer->lectures()
            ->where('is_completed', true)
            ->orderBy('start_time', 'desc')
            ->limit(5)
            ->get();

        // Get active courses
        $activeCourses = Course::whereIn('id', $lecturer->lectureSchedules()
            ->where('is_active', true)
            ->pluck('course_id')
            ->unique())
            ->get();

        // Get lecture statistics
        $stats = [
            'total_lectures' => $lecturer->lectures()->count(),
            'completed_lectures' => $lecturer->lectures()->where('is_completed', true)->count(),
            'upcoming_lectures' => $lecturer->lectures()->where('start_time', '>', now())->count(),
            'today_lectures' => $todayLectures->count(),
            'active_courses' => $activeCourses->count(),
            'students_count' => Student::whereIn('id', function($query) use ($lecturer) {
                $query->select('student_id')
                    ->from('student_courses')
                    ->whereIn('course_id', $lecturer->lectureSchedules()->pluck('course_id'));
            })->count(),
        ];

        // Get attendance statistics by course
        $attendanceByCourseCounts = [];
        $attendanceByCoursePcts = [];

        foreach ($activeCourses as $course) {
            $lectures = Lecture::where('course_id', $course->id)
                ->where('lecturer_id', $lecturer->id)
                ->where('is_completed', true)
                ->get();

            $totalExpected = 0;
            $totalActual = 0;

            foreach ($lectures as $lecture) {
                $totalExpected += $lecture->expected_students;
                $totalActual += $lecture->attendances()->count();
            }

            $attendanceByCourseCounts[$course->code] = [
                'expected' => $totalExpected,
                'actual' => $totalActual
            ];

            $attendanceByCoursePcts[$course->code] = $totalExpected > 0
                ? round(($totalActual / $totalExpected) * 100, 1)
                : 0;
        }

        // Get calendar events (lectures) - only for this lecturer
        $lectures = Lecture::with(['course', 'lecturer'])
            ->where('lecturer_id', $lecturer->id)
            ->whereDate('start_time', '>=', now()->subDays(30))
            ->whereDate('start_time', '<=', now()->addDays(60))
            ->get();

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

            // We'll let the CSS handle the colors through class assignment
            // Just set some extended properties to help with this

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

            // Create formatted calendar event
            $calendarEvents[] = [
                'id' => $lecture->id,
                'title' => $lecture->course->code . ' - ' . $room,
                'start' => $lecture->start_time->toIso8601String(),
                'end' => $lecture->end_time->toIso8601String(),
                'borderColor' => $borderColor,
                'description' => '<strong>Course:</strong> ' . $lecture->course->name .
                                '<br><strong>Room:</strong> ' . $room .
                                '<br><strong>Time:</strong> ' . $lecture->formatted_start_time . ' - ' . $lecture->formatted_end_time .
                                '<br><strong>Duration:</strong> ' . $durationText .
                                '<br><strong>Department:</strong> ' . ($lecture->course->department_name ?? 'N/A'),
                'extendedProps' => [
                    'course_id' => $lecture->course->id,
                    'course_name' => $lecture->course->name,
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

        return view('lecturer.dashboard', compact(
            'upcomingLectures',
            'todayLectures',
            'recentLectures',
            'activeCourses',
            'stats',
            'attendanceByCourseCounts',
            'attendanceByCoursePcts',
            'calendarEvents'
        ));
    }

    /**
     * Display lecturer profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $user = Auth::user();
        $lecturer = $user->lecturer;

        return view('lecturer.profile', compact('user', 'lecturer'));
    }

    /**
     * Update lecturer profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'office_location' => 'nullable|string|max:255',
            'office_hours' => 'nullable|array',
        ]);

        $lecturer = Auth::user()->lecturer;

        $lecturer->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'office_location' => $request->office_location,
            'office_hours' => $request->office_hours,
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $request->validate([
                'profile_photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $file = $request->file('profile_photo');
            $filename = 'lecturer_' . $lecturer->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_photos', $filename, 'public');

            $lecturer->update([
                'profile_photo' => $path,
            ]);
        }

        return redirect()->route('lecturer.profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Update password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update password in the database
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'password' => Hash::make($request->password),
            ]);

        return redirect()->route('lecturer.profile')->with('success', 'Password updated successfully.');
    }

    /**
     * Display lecturers in the same department.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function departmentLecturers(Department $department)
    {
        $lecturers = $department->lecturers->load('user');

        return view('lecturer.department.lecturers', compact('department', 'lecturers'));
    }

    /**
     * Display courses in the department.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function departmentCourses(Department $department)
    {
        $courses = $department->courses;
        $lecturer = Auth::user()->lecturer;

        // Mark courses taught by the current lecturer
        foreach ($courses as &$course) {
            $course->is_teaching = $lecturer->lectureSchedules()
                ->where('course_id', $course->id)
                ->exists();
        }

        return view('lecturer.department.courses', compact('department', 'courses'));
    }

    /**
     * Display students enrolled in a course.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function courseStudents(Course $course)
    {
        $students = $course->students()->wherePivot('status', 'enrolled')->get();

        // Get attendance statistics for each student
        foreach ($students as &$student) {
            $lectures = $course->lectures()
                ->where('lecturer_id', Auth::user()->lecturer->id)
                ->where('is_completed', true)
                ->get();

            $attendedCount = 0;

            foreach ($lectures as $lecture) {
                if ($student->attendedLectures()->where('lecture_id', $lecture->id)->exists()) {
                    $attendedCount++;
                }
            }

            $student->attendance_count = $attendedCount;
            $student->total_lectures = $lectures->count();
            $student->attendance_percentage = $lectures->count() > 0
                ? round(($attendedCount / $lectures->count()) * 100, 1)
                : 0;
        }

        return view('lecturer.courses.students', compact('course', 'students'));
    }

    /**
     * Display attendance report for a course.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function courseAttendanceReport(Course $course)
    {
        $lecturer = Auth::user()->lecturer;

        $lectures = $course->lectures()
            ->where('lecturer_id', $lecturer->id)
            ->where('is_completed', true)
            ->orderBy('start_time')
            ->get();

        $students = $course->students()->wherePivot('status', 'enrolled')->get();

        $attendanceData = [];

        foreach ($students as $student) {
            $attendance = [];

            foreach ($lectures as $lecture) {
                $attended = $student->attendedLectures()->where('lecture_id', $lecture->id)->exists();

                $attendance[] = [
                    'lecture_id' => $lecture->id,
                    'date' => $lecture->formatted_date,
                    'attended' => $attended,
                ];
            }

            $attendedCount = collect($attendance)->where('attended', true)->count();

            $attendanceData[] = [
                'student' => $student,
                'attendance' => $attendance,
                'attended_count' => $attendedCount,
                'total_lectures' => $lectures->count(),
                'percentage' => $lectures->count() > 0
                    ? round(($attendedCount / $lectures->count()) * 100, 1)
                    : 0,
            ];
        }

        return view('lecturer.courses.attendance-report', compact('course', 'lectures', 'attendanceData'));
    }
}
