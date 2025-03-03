<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
        'profile_photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the lecturer associated with the user.
     */
    public function lecturer()
    {
        return $this->hasOne(Lecturer::class);
    }

    /**
     * Get the student associated with the user.
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get the user's full display name
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        // If the user is a lecturer or student, return their specific name
        if ($this->isLecturer() && $this->lecturer) {
            return $this->lecturer->full_name ?? $this->name;
        }

        if ($this->isStudent() && $this->student) {
            return $this->student->full_name ?? $this->name;
        }

        return $this->name;
    }

    /**
     * Get the user's role-specific dashboard route
     *
     * @return string
     */
    public function getDashboardRouteAttribute()
    {
        switch ($this->role) {
            case 'admin':
                return route('admin.dashboard');
            case 'lecturer':
                return route('lecturer.dashboard');
            case 'student':
                return route('student.dashboard');
            default:
                return route('login');
        }
    }

    /**
     * Get the user's role-specific profile route
     *
     * @return string
     */
    public function getProfileRouteAttribute()
    {
        switch ($this->role) {
            case 'admin':
                return route('admin.profile');
            case 'lecturer':
                return route('lecturer.profile');
            case 'student':
                return route('student.profile');
            default:
                return route('login');
        }
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a lecturer.
     */
    public function isLecturer()
    {
        return $this->role === 'lecturer';
    }

    /**
     * Check if the user is a student.
     */
    public function isStudent()
    {
        return $this->role === 'student';
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if the user has permission to access a specific department.
     */
    public function canAccessDepartment($departmentId)
    {
        // Admins can access all departments
        if ($this->isAdmin()) {
            return true;
        }

        // Lecturers can only access their own department
        if ($this->isLecturer()) {
            return $this->lecturer && $this->lecturer->department_id == $departmentId;
        }

        // Students can access departments that their program belongs to
        if ($this->isStudent()) {
            return $this->student && $this->student->program->department_id == $departmentId;
        }

        return false;
    }

    /**
     * Check if the user has permission to access a specific course.
     */
    public function canAccessCourse($courseId)
    {
        // Admins can access all courses
        if ($this->isAdmin()) {
            return true;
        }

        // Lecturers can access courses they teach
        if ($this->isLecturer()) {
            return $this->lecturer && $this->lecturer->lectureSchedules()
                        ->where('course_id', $courseId)
                        ->exists();
        }

        // Students can access courses they're enrolled in
        if ($this->isStudent()) {
            return $this->student && $this->student->courses()
                        ->where('course_id', $courseId)
                        ->wherePivot('status', 'enrolled')
                        ->exists();
        }

        return false;
    }

    /**
     * Check if the user has permission to access a specific lecture.
     */
    public function canAccessLecture($lectureId)
    {
        // Admins can access all lectures
        if ($this->isAdmin()) {
            return true;
        }

        $lecture = Lecture::find($lectureId);
        if (!$lecture) return false;

        // Lecturers can access lectures they're assigned to
        if ($this->isLecturer()) {
            return $this->lecturer && $this->lecturer->id == $lecture->lecturer_id;
        }

        // Students can access lectures for courses they're enrolled in
        if ($this->isStudent()) {
            return $this->canAccessCourse($lecture->course_id);
        }

        return false;
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include users with a specific role.
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }
}
