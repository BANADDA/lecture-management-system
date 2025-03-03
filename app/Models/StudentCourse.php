<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'status',
        'grade',
    ];

    /**
     * Get the student that owns the enrollment.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the course that owns the enrollment.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get formatted status with appropriate styling.
     */
    public function getFormattedStatusAttribute()
    {
        $statusClasses = [
            'enrolled' => 'text-success',
            'completed' => 'text-primary',
            'dropped' => 'text-danger',
        ];

        $statusLabels = [
            'enrolled' => 'Enrolled',
            'completed' => 'Completed',
            'dropped' => 'Dropped',
        ];

        $class = $statusClasses[$this->status] ?? 'text-secondary';
        $label = $statusLabels[$this->status] ?? $this->status;

        return "<span class=\"{$class}\">{$label}</span>";
    }

    /**
     * Get formatted grade.
     */
    public function getFormattedGradeAttribute()
    {
        if (!$this->grade) return '-';

        $gradeLabels = [
            ['min' => 80, 'label' => 'A', 'class' => 'text-success'],
            ['min' => 70, 'label' => 'B', 'class' => 'text-info'],
            ['min' => 60, 'label' => 'C', 'class' => 'text-primary'],
            ['min' => 50, 'label' => 'D', 'class' => 'text-warning'],
            ['min' => 0, 'label' => 'F', 'class' => 'text-danger'],
        ];

        foreach ($gradeLabels as $grade) {
            if ($this->grade >= $grade['min']) {
                return "<span class=\"{$grade['class']}\">{$grade['label']} ({$this->grade}%)</span>";
            }
        }

        return $this->grade . '%';
    }

    /**
     * Scope a query to only include currently enrolled students.
     */
    public function scopeEnrolled($query)
    {
        return $query->where('status', 'enrolled');
    }

    /**
     * Scope a query to only include students who completed the course.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include students who dropped the course.
     */
    public function scopeDropped($query)
    {
        return $query->where('status', 'dropped');
    }
}
