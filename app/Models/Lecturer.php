<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // Added user_id for relationship with User model
        'staff_id',
        'first_name',
        'last_name',
        'phone',
        'department_id',
        'office_location',
        'office_hours',
        'profile_photo',
        'status',
    ];

    protected $casts = [
        'office_hours' => 'array',
    ];

    /**
     * Get the user that owns the lecturer.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department that the lecturer belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the faculty through the department.
     */
    public function faculty()
    {
        return $this->department->faculty();
    }

    /**
     * Get the lecture schedules for the lecturer.
     */
    public function lectureSchedules()
    {
        return $this->hasMany(LectureSchedule::class);
    }

    /**
     * Get the lectures for the lecturer.
     */
    public function lectures()
    {
        return $this->hasMany(Lecture::class);
    }

    /**
     * Get the class representatives appointed by the lecturer.
     */
    public function appointedClassRepresentatives()
    {
        return $this->hasMany(ClassRepresentative::class);
    }

    /**
     * Get the full name.
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get all courses taught by the lecturer.
     */
    public function courses()
    {
        return Course::whereIn('id', $this->lectureSchedules()->pluck('course_id')->unique());
    }

    /**
     * Get active courses for the current semester.
     */
    public function getActiveCoursesAttribute()
    {
        return $this->lectureSchedules()
                   ->where('is_active', true)
                   ->with('course')
                   ->get()
                   ->pluck('course')
                   ->unique('id');
    }

    /**
     * Get upcoming lectures.
     */
    public function getUpcomingLecturesAttribute()
    {
        return $this->lectures()
                   ->where('start_time', '>', now())
                   ->where('is_completed', false)
                   ->orderBy('start_time')
                   ->get();
    }

    /**
     * Get today's lectures.
     */
    public function getTodaysLecturesAttribute()
    {
        return $this->lectures()
                   ->whereDate('start_time', now()->toDateString())
                   ->orderBy('start_time')
                   ->get();
    }

    /**
     * Scope a query to only include active lecturers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include lecturers in a specific department.
     */
    public function scopeInDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
}
