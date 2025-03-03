<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassRepresentative;
use App\Models\Course;
use App\Models\Student;
use App\Models\Lecturer;
use Carbon\Carbon;

class ClassRepresentativeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all courses with lecture schedules
        $courses = Course::whereHas('lectureSchedules')->get();

        if ($courses->isEmpty()) {
            $this->command->error('No courses with lecture schedules found. Please run the LectureScheduleSeeder first.');
            return;
        }

        $repCount = 0;
        $semesterStart = Carbon::create(2024, 2, 1); // February 1, 2024

        foreach ($courses as $course) {
            // Get students enrolled in this course
            $students = $course->students()->where('status', 'active')->get();

            if ($students->isEmpty()) {
                // Skip if no students enrolled
                continue;
            }

            // Get lecturer teaching this course
            $lecturer = $course->lectureSchedules()->first()->lecturer;

            if (!$lecturer) {
                // Skip if no lecturer assigned
                continue;
            }

            // Randomly choose a student to be class rep
            $student = $students->random();

            // Generate random responsibilities
            $responsibilities = $this->getRandomResponsibilities();

            // Create class representative
            ClassRepresentative::create([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'lecturer_id' => $lecturer->id,
                'responsibilities' => $responsibilities,
                'appointed_at' => $semesterStart->copy()->addDays(rand(0, 14)),
                'status' => 'active',
            ]);

            $repCount++;
        }

        $this->command->info("Class representatives created successfully! Total: {$repCount}");
    }

    /**
     * Get random responsibilities for a class representative.
     */
    private function getRandomResponsibilities()
    {
        $responsibilities = [
            "Serve as liaison between students and lecturer",
            "Collect and distribute course materials",
            "Organize study groups and review sessions",
            "Communicate important announcements to classmates",
            "Report issues with classroom facilities",
            "Coordinate with lecturer about schedule changes",
            "Help with attendance tracking",
            "Facilitate group discussions and activities",
            "Assist with class events and presentations",
            "Provide feedback on course content and teaching methods",
        ];

        // Choose 3-5 random responsibilities
        $count = rand(3, 5);
        $selected = array_rand(array_flip($responsibilities), $count);

        return implode("\n- ", array_map(function ($item) {
            return "- " . $item;
        }, $selected));
    }
}
