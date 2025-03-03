<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LectureSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'lecturer_id',
        'room',
        'day_of_week',
        'start_time',
        'end_time',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the course for the lecture schedule.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the lecturer for the lecture schedule.
     */
    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }

    /**
     * Get the lectures generated from this schedule.
     */
    public function lectures()
    {
        return $this->hasMany(Lecture::class);
    }

    /**
     * Get the formatted time slot (e.g. "10:00 AM - 12:00 PM").
     */
    public function getFormattedTimeSlotAttribute()
    {
        return Carbon::parse($this->start_time)->format('h:i A') . ' - ' .
               Carbon::parse($this->end_time)->format('h:i A');
    }

    /**
     * Get the duration in hours and minutes.
     */
    public function getDurationAttribute()
    {
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        $diff = $start->diff($end);

        $hours = $diff->h;
        $minutes = $diff->i;

        if ($hours > 0) {
            return $hours . ' hr' . ($hours > 1 ? 's' : '') .
                   ($minutes > 0 ? ' ' . $minutes . ' min' : '');
        } else {
            return $minutes . ' min';
        }
    }

    /**
     * Generate all lecture instances for this schedule within its date range.
     */
    public function generateLectures()
    {
        $lectures = [];
        $currentDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);

        while ($currentDate->lte($endDate)) {
            // Check if the current day matches the schedule day
            if ($currentDate->englishDayOfWeek === $this->day_of_week) {
                // Parse the time portions
                $startTime = Carbon::parse($this->start_time)->format('H:i:s');
                $endTime = Carbon::parse($this->end_time)->format('H:i:s');

                // Create the full datetime objects
                $startDateTime = Carbon::parse($currentDate->format('Y-m-d') . ' ' . $startTime);
                $endDateTime = Carbon::parse($currentDate->format('Y-m-d') . ' ' . $endTime);

                // Create the lecture
                $lectures[] = Lecture::create([
                    'course_id' => $this->course_id,
                    'lecturer_id' => $this->lecturer_id,
                    'lecture_schedule_id' => $this->id,
                    'room' => $this->room,
                    'start_time' => $startDateTime,
                    'end_time' => $endDateTime,
                    'expected_students' => $this->course->students_count,
                ]);
            }

            $currentDate->addDay();
        }

        return $lectures;
    }
}
