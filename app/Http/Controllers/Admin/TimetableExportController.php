<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Carbon\Carbon;
use App\Models\Lecture;
use Illuminate\Http\Request;

class TimetableExportController extends Controller
{
    /**
     * Export weekly timetable as PDF.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportPDF(Request $request)
    {
        // Get week and year from request or use current week
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : now();
        $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $date->copy()->endOfWeek(Carbon::FRIDAY); // Only Monday to Friday

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

        // Week info
        $weekInfo = [
            'start' => $weekStart->format('M d, Y'),
            'end' => $weekEnd->format('M d, Y'),
        ];

        // Format period time ranges for timeslots
        $timeSlots = $this->generateTimeSlots();

        // Calculate lecture positions in the grid based on time
        $timetableGrid = $this->organizeIntoGrid($timetableData, $timeSlots);

        // Generate PDF with a specific view
        $pdf = PDF::loadView('admin.exports.weekly-timetable-pdf', [
            'timetableData' => $timetableData,
            'timetableGrid' => $timetableGrid,
            'timeSlots' => $timeSlots,
            'weekInfo' => $weekInfo,
            'days' => $days
        ]);

        // Set PDF options for landscape orientation and A4 size
        $pdf->setPaper('a4', 'landscape');

        // Return the PDF for download
        return $pdf->download("Weekly_Timetable_{$weekStart->format('Y-m-d')}_to_{$weekEnd->format('Y-m-d')}.pdf");
    }

    /**
     * Generate time slots for timetable grid
     */
    private function generateTimeSlots()
    {
        $startTime = Carbon::createFromTime(8, 0); // 8:00 AM
        $endTime = Carbon::createFromTime(18, 0);  // 6:00 PM
        $interval = 60; // 60 minutes

        $timeSlots = [];
        $current = $startTime->copy();

        while ($current < $endTime) {
            $timeSlots[] = [
                'start' => $current->format('H:i'),
                'end' => $current->copy()->addMinutes($interval)->format('H:i'),
                'label' => $current->format('g:i A') . ' - ' . $current->copy()->addMinutes($interval)->format('g:i A')
            ];
            $current->addMinutes($interval);
        }

        return $timeSlots;
    }

    /**
     * Organize lectures into a grid format based on time slots
     */
    private function organizeIntoGrid($timetableData, $timeSlots)
    {
        $days = array_keys($timetableData);
        $grid = [];

        // Initialize empty grid
        foreach ($timeSlots as $slotIndex => $slot) {
            $grid[$slotIndex] = [];
            foreach ($days as $day) {
                $grid[$slotIndex][$day] = null;
            }
        }

        // Place lectures in the grid
        foreach ($days as $day) {
            foreach ($timetableData[$day]['lectures'] as $lecture) {
                // Find the appropriate time slot
                $startTime = Carbon::createFromFormat('h:i A', $lecture['start_time']);

                foreach ($timeSlots as $slotIndex => $slot) {
                    $slotStart = Carbon::createFromFormat('H:i', $slot['start']);
                    $slotEnd = Carbon::createFromFormat('H:i', $slot['end']);

                    // If lecture starts within this slot
                    if ($startTime->format('H:i') >= $slotStart->format('H:i') &&
                        $startTime->format('H:i') < $slotEnd->format('H:i')) {
                        $grid[$slotIndex][$day] = $lecture;
                        break;
                    }
                }
            }
        }

        return $grid;
    }
}
