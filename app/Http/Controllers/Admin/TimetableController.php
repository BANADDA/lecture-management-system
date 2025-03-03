<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;
use App\Models\Lecture;

class TimetableController extends Controller
{
    public function index()
    {
        $date = request('date', now());
        $data = $this->generateTimetableData($date);
        return view('admin.timetable.index', $data);
    }

    public function exportPDF(Request $request)
    {
        $date = $request->query('date', now());
        $data = $this->generateTimetableData($date);
        $pdf = PDF::loadView('admin.timetable.index', $data);
        return $pdf->download("weekly_timetable.pdf");
    }

    protected function generateTimetableData($date)
    {
        $date = Carbon::parse($date);
        $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->addDays(4);
        $weekInfo = [
            'start' => $weekStart->format('M d, Y'),
            'end'   => $weekEnd->format('M d, Y')
        ];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $timetableData = [];
        foreach ($days as $i => $day) {
            $currentDate = $weekStart->copy()->addDays($i);
            $lectures = Lecture::whereDate('start_time', $currentDate->format('Y-m-d'))
                ->orderBy('start_time')
                ->get();
            $timetableData[$day] = [
                'formatted_date' => $currentDate->format('M d, Y'),
                'lectures'       => $lectures
            ];
        }
        $timeSlotsCollection = collect();
        foreach ($days as $day) {
            foreach ($timetableData[$day]['lectures'] as $lecture) {
                $slotKey = $lecture->start_time->format('H:i') . '-' . $lecture->end_time->format('H:i');
                if (!$timeSlotsCollection->has($slotKey)) {
                    $timeSlotsCollection->put($slotKey, [
                        'label' => $lecture->start_time->format('H:i') . ' - ' . $lecture->end_time->format('H:i'),
                        'start_time' => $lecture->start_time->format('H:i'),
                        'end_time'   => $lecture->end_time->format('H:i')
                    ]);
                }
            }
        }
        $timeSlots = $timeSlotsCollection->values()->all();
        usort($timeSlots, function($a, $b) {
            return strcmp($a['start_time'], $b['start_time']);
        });
        $timetableGrid = [];
        foreach ($timeSlots as $slotIndex => $slot) {
            foreach ($days as $day) {
                foreach ($timetableData[$day]['lectures'] as $lecture) {
                    if ($lecture->start_time->format('H:i') == $slot['start_time'] && $lecture->end_time->format('H:i') == $slot['end_time']) {
                        $timetableGrid[$slotIndex][$day] = [
                            'start_time' => $lecture->formatted_start_time,
                            'end_time' => $lecture->formatted_end_time,
                            'course_code' => $lecture->course_code,
                            'course_name' => $lecture->course_name,
                            'room' => $lecture->room,
                            'lecturer' => $lecture->lecturer_name,
                            'attendance_percentage' => $lecture->attendance_percentage
                        ];
                    }
                }
            }
        }
        return [
            'weekInfo' => $weekInfo,
            'timetableData' => $timetableData,
            'days' => $days,
            'timeSlots' => $timeSlots,
            'timetableGrid' => $timetableGrid
        ];
    }
}
