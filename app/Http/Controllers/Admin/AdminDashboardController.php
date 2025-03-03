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

        return view('admin.dashboard', compact('stats', 'lecturesByDay', 'attendanceStats'));
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
    $user = auth()->user();
    return view('admin.profile', compact('user'));
}

public function updateProfile(Request $request)
{
    $user = auth()->user();

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

    $user->save();

    return redirect()->route('admin.profile')->with('success', 'Profile updated successfully');
}

public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'password' => 'required|min:8|confirmed',
    ]);

    $user = auth()->user();

    // Check if current password matches
    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Current password is incorrect']);
    }

    // Update password
    $user->password = Hash::make($request->password);
    $user->save();

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
}
