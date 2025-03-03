<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'image_url',
    ];

    /**
     * Get the departments for the faculty.
     */
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get all programs through departments.
     */
    public function programs()
    {
        return $this->hasManyThrough(Program::class, Department::class);
    }

    /**
     * Get the total number of students in the faculty.
     */
    public function getStudentsCountAttribute()
    {
        $count = 0;
        foreach ($this->departments as $department) {
            $count += $department->students_count;
        }
        return $count;
    }
}
