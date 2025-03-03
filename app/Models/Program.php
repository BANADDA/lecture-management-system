<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name',
        'code',
        'duration_years',
        'description',
        'image_url',
    ];

    /**
     * Get the department that owns the program.
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
     * Get the students for the program.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get the courses for the program.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get courses grouped by year and semester.
     */
    public function getCoursesByYearAndSemesterAttribute()
    {
        $coursesByYearSemester = [];

        for ($year = 1; $year <= $this->duration_years; $year++) {
            $coursesByYearSemester[$year] = [
                1 => $this->courses()->where('year', $year)->where('semester', 1)->get(),
                2 => $this->courses()->where('year', $year)->where('semester', 2)->get(),
            ];
        }

        return $coursesByYearSemester;
    }
}
