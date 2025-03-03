<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRepresentative extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'lecturer_id',
        'responsibilities',
        'appointed_at',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'appointed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the student who is the class representative.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the course for which the student is a class representative.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the lecturer who appointed the class representative.
     */
    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }

    /**
     * Check if the class representative is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Get the formatted appointment date.
     */
    public function getFormattedAppointedAtAttribute()
    {
        return $this->appointed_at->format('M d, Y');
    }

    /**
     * Get the formatted expiration date.
     */
    public function getFormattedExpiresAtAttribute()
    {
        return $this->expires_at ? $this->expires_at->format('M d, Y') : 'N/A';
    }

    /**
     * Get the service duration in months or days.
     */
    public function getServiceDurationAttribute()
    {
        $endDate = $this->expires_at ?? now();
        $months = $this->appointed_at->diffInMonths($endDate);

        if ($months > 0) {
            return $months . ' ' . ($months == 1 ? 'month' : 'months');
        } else {
            $days = $this->appointed_at->diffInDays($endDate);
            return $days . ' ' . ($days == 1 ? 'day' : 'days');
        }
    }

    /**
     * Scope a query to only include active class representatives.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive class representatives.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope a query to only include class representatives for a specific course.
     */
    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope a query to only include class representatives appointed by a specific lecturer.
     */
    public function scopeAppointedBy($query, $lecturerId)
    {
        return $query->where('lecturer_id', $lecturerId);
    }
}
