<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LectureAttendanceExport;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lecture;
use App\Models\Lecturer;
use App\Models\LectureSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class LectureController extends Controller
{
    /**
     * Display a listing of the lectures.
     */
    public function index(Request $request)
    {
        // Filter parameters
        $filters = [
            'course_id' => $request->input('course_id'),
            'lecturer_id' => $request->input('lecturer_id'),
            'date' => $request->input('date'),
            'status' => $request->input('status', 'all'), // all, upcoming, past, today
            'search' => $request->input('search'),
        ];

        // Start with a base query with relationships
        $query = Lecture::with(['course', 'lecturer', 'lectureSchedule']);

        // Apply filters
        if ($filters['course_id']) {
            $query->where('course_id', $filters['course_id']);
        }

        if ($filters['lecturer_id']) {
            $query->where('lecturer_id', $filters['lecturer_id']);
        }

        if ($filters['date']) {
            $query->whereDate('start_time', $filters['date']);
        }

        // Apply status filter
        switch ($filters['status']) {
            case 'upcoming':
                $query->upcoming();
                break;
            case 'past':
                $query->past();
                break;
            case 'today':
                $query->today();
                break;
            default:
                $query->orderBy('start_time', 'desc');
                break;
        }

        // Apply search
        if ($filters['search']) {
            $search = $filters['search'];
            $query->whereHas('course', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })->orWhereHas('lecturer', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            })->orWhere('room', 'like', "%{$search}%");
        }

        // Get paginated results
        $lectures = $query->paginate(10);

        // Get data for filter dropdowns
        $courses = Course::orderBy('name')->get();
        $lecturers = Lecturer::orderBy('last_name')->get();

        return view('admin.lectures.index', compact('lectures', 'courses', 'lecturers', 'filters'));
    }

    /**
     * Show the form for creating a new lecture.
     */
    public function create()
    {
        $courses = Course::orderBy('name')->get();
        $lecturers = Lecturer::orderBy('last_name')->get();
        $lectureSchedules = LectureSchedule::orderBy('day_of_week')->get();

        return view('admin.lectures.create', compact('courses', 'lecturers', 'lectureSchedules'));
    }

    /**
     * Store a newly created lecture in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'lecturer_id' => 'required|exists:lecturers,id',
            'lecture_schedule_id' => 'nullable|exists:lecture_schedules,id',
            'room' => 'required|string|max:50',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'expected_students' => 'required|integer|min:0',
            'is_completed' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validatedData['image_url'] = $request->file('image')->store('lectures', 'public');
        }

        // Set default values
        $validatedData['is_completed'] = $request->has('is_completed');

        DB::beginTransaction();
        try {
            $lecture = Lecture::create($validatedData);
            DB::commit();

            return redirect()->route('admin.lectures.show', $lecture)
                ->with('success', 'Lecture created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Failed to create lecture: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified lecture.
     */
    public function show(Lecture $lecture)
    {
        // Load relationships
        $lecture->load(['course', 'lecturer', 'attendees']);

        return view('admin.lectures.show', compact('lecture'));
    }

    /**
     * Show the form for editing the specified lecture.
     */
    public function edit(Lecture $lecture)
    {
        $courses = Course::orderBy('name')->get();
        $lecturers = Lecturer::orderBy('last_name')->get();
        $lectureSchedules = LectureSchedule::orderBy('day_of_week')->get();

        return view('admin.lectures.edit', compact('lecture', 'courses', 'lecturers', 'lectureSchedules'));
    }

    /**
     * Update the specified lecture in storage.
     */
    public function update(Request $request, Lecture $lecture)
    {
        $validatedData = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'lecturer_id' => 'required|exists:lecturers,id',
            'lecture_schedule_id' => 'nullable|exists:lecture_schedules,id',
            'room' => 'required|string|max:50',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'expected_students' => 'required|integer|min:0',
            'is_completed' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($lecture->image_url) {
                Storage::disk('public')->delete($lecture->image_url);
            }

            $validatedData['image_url'] = $request->file('image')->store('lectures', 'public');
        }

        // Set default values
        $validatedData['is_completed'] = $request->has('is_completed');

        DB::beginTransaction();
        try {
            $lecture->update($validatedData);
            DB::commit();

            return redirect()->route('admin.lectures.show', $lecture)
                ->with('success', 'Lecture updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Failed to update lecture: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified lecture from storage.
     */
    public function destroy(Lecture $lecture)
    {
        DB::beginTransaction();
        try {
            // Delete image if exists
            if ($lecture->image_url) {
                Storage::disk('public')->delete($lecture->image_url);
            }

            $lecture->delete();
            DB::commit();

            return redirect()->route('admin.lectures.index')
                ->with('success', 'Lecture deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete lecture: ' . $e->getMessage());
        }
    }

    /**
     * Mark a lecture as completed.
     */
    public function markCompleted(Lecture $lecture)
    {
        $lecture->update(['is_completed' => true]);

        return redirect()->back()->with('success', 'Lecture marked as completed');
    }

    /**
     * Show attendance for a specific lecture.
     */
    public function showAttendance(Lecture $lecture)
    {
        $lecture->load(['attendees', 'course']);

        return view('admin.lectures.attendance', compact('lecture'));
    }

    /**
 * Export weekly timetable as PDF.
 */
public function exportWeeklyTimetable(Request $request)
{
    // Get week and year from request or use current week
    $date = $request->input('date') ? Carbon::parse($request->input('date')) : now();
    $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY);
    $weekEnd = $date->copy()->endOfWeek(Carbon::SUNDAY);

    // Get all lectures for the selected week
    $lectures = Lecture::whereBetween('start_time', [$weekStart, $weekEnd])
        ->with(['course', 'lecturer'])
        ->orderBy('start_time')
        ->get();

    // Organize lectures by day of week
    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
    $timetableData = [];

    foreach ($days as $day) {
        $dayDate = $weekStart->copy()->addDays(array_search($day, $days));
        $timetableData[$day] = [
            'date' => $dayDate->format('Y-m-d'),
            'formatted_date' => $dayDate->format('D, M d'),
            'lectures' => []
        ];
    }

    // Populate the timetable with lectures
    foreach ($lectures as $lecture) {
        $day = strtolower($lecture->start_time->format('l'));
        if (in_array($day, $days)) {
            $timetableData[$day]['lectures'][] = [
                'id' => $lecture->id,
                'course_code' => $lecture->course_code,
                'course_name' => $lecture->course_name,
                'lecturer' => $lecture->lecturer_name,
                'room' => $lecture->room,
                'start_time' => $lecture->formatted_start_time,
                'end_time' => $lecture->formatted_end_time,
                'duration' => $lecture->duration,
                'attendance_percentage' => $lecture->attendance_percentage
            ];
        }
    }

    // Week info for the PDF
    $weekInfo = [
        'start' => $weekStart->format('M d, Y'),
        'end' => $weekEnd->format('M d, Y'),
    ];

    // Make sure PDF class is properly imported at the top of the file
    // use Barryvdh\DomPDF\Facade\Pdf;

    $pdf = PDF::loadView('admin.exports.weekly-timetable', compact('timetableData', 'weekInfo'));

    return $pdf->download("Weekly_Timetable_{$weekStart->format('Y-m-d')}_to_{$weekEnd->format('Y-m-d')}.pdf");
}

    // In LectureController.php - Add a new method for weekly timetable
public function weeklyTimetable(Request $request)
{
    // Get week and year from request or use current week
    $date = $request->input('date') ? Carbon::parse($request->input('date')) : now();
    $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY);
    $weekEnd = $date->copy()->endOfWeek(Carbon::SUNDAY);

    // Get all lectures for the selected week
    $lectures = Lecture::whereBetween('start_time', [$weekStart, $weekEnd])
        ->with(['course', 'lecturer'])
        ->orderBy('start_time')
        ->get();

    // Organize lectures by day of week
    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
    $timetableData = [];

    foreach ($days as $day) {
        $dayDate = $weekStart->copy()->addDays(array_search($day, $days));
        $timetableData[$day] = [
            'date' => $dayDate->format('Y-m-d'),
            'formatted_date' => $dayDate->format('D, M d'),
            'lectures' => []
        ];
    }

    // Populate the timetable with lectures
    foreach ($lectures as $lecture) {
        $day = strtolower($lecture->start_time->format('l'));
        if (in_array($day, $days)) {
            $timetableData[$day]['lectures'][] = [
                'id' => $lecture->id,
                'course_code' => $lecture->course_code,
                'course_name' => $lecture->course_name,
                'lecturer' => $lecture->lecturer_name,
                'room' => $lecture->room,
                'start_time' => $lecture->formatted_start_time,
                'end_time' => $lecture->formatted_end_time,
                'duration' => $lecture->duration,
                'attendance_percentage' => $lecture->attendance_percentage
            ];
        }
    }

    // Current week info
    $weekInfo = [
        'start' => $weekStart->format('M d, Y'),
        'end' => $weekEnd->format('M d, Y'),
        'previous' => $weekStart->copy()->subWeek()->format('Y-m-d'),
        'next' => $weekStart->copy()->addWeek()->format('Y-m-d'),
        'current' => $date->format('Y-m-d'),
    ];

    return view('admin.lectures.weekly-timetable', compact('timetableData', 'weekInfo'));
}

    /**
 * Export monthly timetable.
 */
public function exportMonthlyTimetable(Request $request)
{
    // Get month and year from request or use current month
    $month = $request->input('month', now()->month);
    $year = $request->input('year', now()->year);

    // Create Carbon instances for the start and end of the month
    $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
    $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

    // Get all lectures for the specified month
    $lectures = Lecture::whereBetween('start_time', [$startDate, $endDate])
        ->with(['course', 'lecturer'])
        ->orderBy('start_time')
        ->get();

    // Organize lectures by day
    $timetableData = [];
    $weeksInMonth = [];

    // Generate weeks array for the month
    $currentDate = $startDate->copy();
    while ($currentDate <= $endDate) {
        $weekNumber = $currentDate->weekOfYear;
        if (!in_array($weekNumber, $weeksInMonth)) {
            $weeksInMonth[] = $weekNumber;
        }
        $currentDate->addDay();
    }

    // Initialize the timetable data structure
    foreach ($weeksInMonth as $week) {
        $timetableData[$week] = [
            'monday' => [],
            'tuesday' => [],
            'wednesday' => [],
            'thursday' => [],
            'friday' => [],
            'saturday' => [],
            'sunday' => [],
        ];
    }

    // Populate the timetable with lectures
    foreach ($lectures as $lecture) {
        $day = strtolower($lecture->start_time->format('l'));
        $week = $lecture->start_time->weekOfYear;

        $timetableData[$week][$day][] = [
            'id' => $lecture->id,
            'course_code' => $lecture->course_code,
            'course_name' => $lecture->course_name,
            'lecturer' => $lecture->lecturer_name,
            'room' => $lecture->room,
            'start_time' => $lecture->formatted_start_time,
            'end_time' => $lecture->formatted_end_time,
            'duration' => $lecture->duration,
            'date' => $lecture->start_time->format('Y-m-d'),
            'attendance_percentage' => $lecture->attendance_percentage
        ];
    }

    // Month and year information
    $monthName = $startDate->format('F');
    $yearValue = $year;

    // Generate PDF view
    $pdf = PDF::loadView('admin.exports.monthly-timetable', compact(
        'timetableData',
        'weeksInMonth',
        'monthName',
        'yearValue'
    ));

    // Return the PDF for download
    return $pdf->download("Timetable-{$monthName}-{$yearValue}.pdf");
}

/**
 * Display the monthly timetable view.
 */
public function timetable()
{
    return view('admin.lectures.timetable');
}

    /**
     * Export attendance as CSV.
     */
    public function exportAttendance(Lecture $lecture)
{
    // Load related data
    $lecture->load([
        'course',
        'lecturer',
        'students' => function ($query) {
            $query->withPivot('check_in_time', 'check_in_method', 'comment');
        }
    ]);

    // Prepare the data for export
    $attendanceData = $lecture->students->map(function ($student) {
        return [
            'Student ID' => $student->id,
            'Name' => $student->full_name,
            'Program' => $student->program->name ?? 'N/A',
            'Check-in Time' => $student->pivot->check_in_time
                ? Carbon::parse($student->pivot->check_in_time)->format('Y-m-d H:i:s')
                : 'Not checked in',
            'Check-in Method' => $student->pivot->check_in_method ?? 'N/A',
            'Comment' => $student->pivot->comment ?? ''
        ];
    });

    // Generate filename
    $filename = "Lecture_{$lecture->id}_Attendance_" . now()->format('YmdHis') . ".xlsx";

    // Export using Laravel Excel
    return Excel::download(new LectureAttendanceExport($attendanceData), $filename);
}
}
