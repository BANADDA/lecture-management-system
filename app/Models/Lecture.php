<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Lecture extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'lecturer_id',
        'lecture_schedule_id',
        'room',
        'start_time',
        'end_time',
        'expected_students',
        'is_completed',
        'image_url',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_completed' => 'boolean',
    ];

    /**
     * Get the course for the lecture.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the lecturer for the lecture.
     */
    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }

    /**
     * Get the lecture schedule that generated this lecture.
     */
    public function lectureSchedule()
    {
        return $this->belongsTo(LectureSchedule::class);
    }

    /**
     * Get the attendances for the lecture.
     */
    public function attendances()
    {
        return $this->hasMany(LectureAttendance::class);
    }

    /**
     * Get the students who attended the lecture.
     */
    public function attendees()
    {
        return $this->belongsToMany(Student::class, 'lecture_attendances')
                    ->withPivot('check_in_time', 'check_in_method', 'comment')
                    ->withTimestamps();
    }

    /**
     * Get the formatted date (e.g., "Mon, Jan 1, 2023").
     */
    public function getFormattedDateAttribute()
    {
        return $this->start_time->format('D, M d, Y');
    }

    /**
     * Get the formatted start time (e.g., "10:00 AM").
     */
    public function getFormattedStartTimeAttribute()
    {
        return $this->start_time->format('h:i A');
    }

    /**
     * Get the formatted end time (e.g., "12:00 PM").
     */
    public function getFormattedEndTimeAttribute()
    {
        return $this->end_time->format('h:i A');
    }

    /**
     * Get the duration in hours and minutes.
     */
    public function getDurationAttribute()
    {
        $diff = $this->start_time->diff($this->end_time);
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
     * Get the course name.
     */
    public function getCourseNameAttribute()
    {
        return $this->course->name;
    }

    /**
     * Get the course code.
     */
    public function getCourseCodeAttribute()
    {
        return $this->course->code;
    }

    /**
     * Get the department name.
     */
    public function getDepartmentAttribute()
    {
        return $this->course->department_name;
    }

    /**
     * Get the faculty name.
     */
    public function getFacultyAttribute()
    {
        return $this->course->faculty_name;
    }

    /**
     * Get the lecturer name.
     */
    public function getLecturerNameAttribute()
    {
        return $this->lecturer->full_name;
    }

    /**
     * Get the actual attendance count.
     */
    public function getActualAttendanceAttribute()
    {
        return $this->attendances->count();
    }

    /**
     * Get the attendance percentage.
     */
    public function getAttendancePercentageAttribute()
    {
        if ($this->expected_students == 0) return 0;

        return ($this->actual_attendance / $this->expected_students) * 100;
    }

    /**
     * Check if the lecture is ongoing.
     */
    public function getIsOngoingAttribute()
    {
        $now = Carbon::now();
        return $now->between($this->start_time, $this->end_time);
    }

    /**
     * Check if the lecture is completed.
     */
    public function getIsCompletedAttribute()
    {
        return $this->attributes['is_completed'] || Carbon::now()->isAfter($this->end_time);
    }

    /**
     * Scope a query to only include upcoming lectures.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', Carbon::now())
                     ->where('is_completed', false)
                     ->orderBy('start_time');
    }

    /**
     * Scope a query to only include past lectures.
     */
    public function scopePast($query)
    {
        return $query->where(function($q) {
                        $q->where('is_completed', true)
                          ->orWhere('end_time', '<', Carbon::now());
                     })
                     ->orderBy('start_time', 'desc');
    }

    /**
     * Scope a query to only include today's lectures.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('start_time', Carbon::today())
                     ->orderBy('start_time');
    }
}
