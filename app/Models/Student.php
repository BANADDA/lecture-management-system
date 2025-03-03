<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // Added user_id for relationship with User model
        'student_id',
        'first_name',
        'last_name',
        'phone',
        'program_id',
        'current_year',
        'current_semester',
        'profile_photo',
        'status',
    ];

    /**
     * Get the user that owns the student.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the program that the student is enrolled in.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the department through the program.
     */
    public function department()
    {
        return $this->program->department();
    }

    /**
     * Get the faculty through the program's department.
     */
    public function faculty()
    {
        return $this->program->department->faculty();
    }

    /**
     * Get the courses that the student is enrolled in.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'student_courses')
                    ->withPivot('status', 'grade')
                    ->withTimestamps();
    }

    /**
     * Get the lecture attendances for the student.
     */
    public function lectureAttendances()
    {
        return $this->hasMany(LectureAttendance::class);
    }

    /**
     * Get the attended lectures.
     */
    public function attendedLectures()
    {
        return $this->belongsToMany(Lecture::class, 'lecture_attendances')
                    ->withPivot('check_in_time', 'check_in_method', 'comment')
                    ->withTimestamps();
    }

    /**
     * Get the class representative roles.
     */
    public function classRepresentativeRoles()
    {
        return $this->hasMany(ClassRepresentative::class);
    }

    /**
     * Check if the student is a class representative for a specific course.
     */
    public function isClassRepresentative($courseId = null)
    {
        $query = $this->classRepresentativeRoles()->where('status', 'active');

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        return $query->exists();
    }

    /**
     * Get the full name.
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the attendance rate for a specific course.
     */
    public function getAttendanceRateAttribute($courseId)
    {
        $course = Course::find($courseId);
        if (!$course) return 0;

        $totalLectures = $course->lectures()->where('is_completed', true)->count();
        if ($totalLectures == 0) return 0;

        $attendedLectures = $this->attendedLectures()
                               ->where('course_id', $courseId)
                               ->where('is_completed', true)
                               ->count();

        return ($attendedLectures / $totalLectures) * 100;
    }

    /**
     * Get all active courses for the current semester.
     */
    public function getCurrentCoursesAttribute()
    {
        return $this->courses()
                    ->where('year', $this->current_year)
                    ->where('semester', $this->current_semester)
                    ->wherePivot('status', 'enrolled')
                    ->get();
    }

    /**
     * Scope a query to only include active students.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include students in a specific program.
     */
    public function scopeInProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }

    /**
     * Scope a query to only include students in a specific year.
     */
    public function scopeInYear($query, $year)
    {
        return $query->where('current_year', $year);
    }

    /**
     * Scope a query to only include students in a specific semester.
     */
    public function scopeInSemester($query, $semester)
    {
        return $query->where('current_semester', $semester);
    }
}
