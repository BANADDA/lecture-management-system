<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'faculty_id',
        'name',
        'code',
        'description',
        'campus',
        'image_url',
    ];

    /**
     * Get the faculty that owns the department.
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the programs for the department.
     */
    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    /**
     * Get the lecturers for the department.
     */
    public function lecturers()
    {
        return $this->hasMany(Lecturer::class);
    }

    /**
     * Get all courses through programs.
     */
    public function courses()
    {
        return $this->hasManyThrough(Course::class, Program::class);
    }

    /**
     * Get the total number of students in the department.
     */
    public function getStudentsCountAttribute()
    {
        $count = 0;
        foreach ($this->programs as $program) {
            $count += $program->students->count();
        }
        return $count;
    }

    /**
     * Get the list of course names in the department.
     */
    public function getCoursesListAttribute()
    {
        return $this->courses()->pluck('name')->toArray();
    }
}
