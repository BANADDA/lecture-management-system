<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LectureAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'lecture_id',
        'student_id',
        'check_in_time',
        'check_in_method',
        'comment',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
    ];

    /**
     * Get the lecture that owns the attendance record.
     */
    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }

    /**
     * Get the student that owns the attendance record.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the course through the lecture.
     */
    public function course()
    {
        return $this->lecture->course();
    }

    /**
     * Get the lecturer through the lecture.
     */
    public function lecturer()
    {
        return $this->lecture->lecturer();
    }

    /**
     * Get the formatted check-in time.
     */
    public function getFormattedCheckInTimeAttribute()
    {
        return $this->check_in_time->format('h:i A');
    }

    /**
     * Check if the student checked in on time.
     */
    public function getIsOnTimeAttribute()
    {
        // Consider a 15-minute grace period
        $lateThreshold = $this->lecture->start_time->copy()->addMinutes(15);
        return $this->check_in_time->lte($lateThreshold);
    }

    /**
     * Get how late the student was (in minutes).
     */
    public function getLateByMinutesAttribute()
    {
        if ($this->is_on_time) return 0;

        return $this->check_in_time->diffInMinutes($this->lecture->start_time);
    }

    /**
     * Scope a query to only include attendances for a specific course.
     */
    public function scopeForCourse($query, $courseId)
    {
        return $query->whereHas('lecture', function ($q) use ($courseId) {
            $q->where('course_id', $courseId);
        });
    }

    /**
     * Scope a query to only include attendances for a specific student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to only include today's attendances.
     */
    public function scopeToday($query)
    {
        return $query->whereHas('lecture', function ($q) {
            $q->whereDate('start_time', now()->toDateString());
        });
    }
}
