<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'code',
        'name',
        'description',
        'year',
        'semester',
        'credits',
        'image_url',
    ];

    /**
     * Get the program that owns the course.
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
     * Get the lecture schedules for the course.
     */
    public function lectureSchedules()
    {
        return $this->hasMany(LectureSchedule::class);
    }

    /**
     * Get the lectures for the course.
     */
    public function lectures()
    {
        return $this->hasMany(Lecture::class);
    }

    /**
     * Get the students enrolled in this course.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_courses')
                    ->withPivot('status', 'grade')
                    ->withTimestamps();
    }

    /**
     * Get the class representative for this course.
     */
    public function classRepresentative()
    {
        return $this->hasOne(ClassRepresentative::class)
                    ->where('status', 'active');
    }

    /**
     * Get the formatted level (e.g., "100 Level").
     */
    public function getLevelAttribute()
    {
        return ($this->year * 100) . " Level";
    }

    /**
     * Get the formatted semester (e.g., "1st Semester").
     */
    public function getSemesterNameAttribute()
    {
        $suffix = $this->semester == 1 ? 'st' : 'nd';
        return $this->semester . $suffix . ' Semester';
    }

    /**
     * Get the number of students enrolled in this course.
     */
    public function getStudentsCountAttribute()
    {
        return $this->students()->where('status', 'enrolled')->count();
    }

    /**
     * Get the department name.
     */
    public function getDepartmentNameAttribute()
    {
        return $this->program->department->name;
    }

    /**
     * Get the faculty name.
     */
    public function getFacultyNameAttribute()
    {
        return $this->program->department->faculty->name;
    }
}
