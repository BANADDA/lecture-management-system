<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LectureSchedule;
use App\Models\Lecture;
use App\Models\Course;
use App\Models\Lecturer;
use Carbon\Carbon;

class LectureScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all courses and lecturers
        $courses = Course::all();
        $lecturers = Lecturer::all();

        if ($courses->isEmpty()) {
            $this->command->error('No courses found. Please run the CourseSeeder first.');
            return;
        }

        if ($lecturers->isEmpty()) {
            $this->command->error('No lecturers found. Please run the UsersSeeder first.');
            return;
        }

        // Define semester dates
        $semesterStart = Carbon::create(2024, 2, 1); // February 1, 2024
        $semesterEnd = Carbon::create(2024, 5, 31);  // May 31, 2024

        // Classrooms
        $classrooms = [
            'LH 101', 'LH 102', 'LH 103', 'LH 201', 'LH 202', 'LH 203',
            'CS Lab 1', 'CS Lab 2', 'Engineering Lab', 'Business Lab',
            'Room A1', 'Room A2', 'Room B1', 'Room B2'
        ];

        // Days of the week
        $daysOfWeek = [
            'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'
        ];

        // Create lecture schedules
        $scheduleCount = 0;
        $coursesWithSchedules = [];

        foreach ($courses as $course) {
            // Assign between 1-2 schedules per course (to avoid too many)
            $numSchedules = rand(1, 2);

            // Ensure we don't create too many schedules for small departments
            if (count($coursesWithSchedules) >= 50) {
                break;
            }

            for ($i = 0; $i < $numSchedules; $i++) {
                // Select a random lecturer from the same department as the course
                $departmentId = $course->program->department_id;
                $departmentLecturers = $lecturers->where('department_id', $departmentId);

                // If no lecturer in that department, use any lecturer
                $lecturer = $departmentLecturers->isNotEmpty()
                    ? $departmentLecturers->random()
                    : $lecturers->random();

                // Select a random day, time slot, and classroom
                $day = $daysOfWeek[array_rand($daysOfWeek)];
                $startHour = rand(8, 15); // 8 AM to 3 PM
                $duration = rand(1, 3); // 1 to 3 hour duration
                $classroom = $classrooms[array_rand($classrooms)];

                // Format the times as strings
                $startTime = sprintf('%02d:00:00', $startHour);
                $endTime = sprintf('%02d:00:00', $startHour + $duration);

                // Create the schedule
                LectureSchedule::create([
                    'course_id' => $course->id,
                    'lecturer_id' => $lecturer->id,
                    'room' => $classroom,
                    'day_of_week' => $day,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'start_date' => $semesterStart,
                    'end_date' => $semesterEnd,
                    'is_active' => true,
                ]);

                $scheduleCount++;
                $coursesWithSchedules[] = $course->id;
            }
        }

        $this->command->info("Lecture schedules created successfully! Total: {$scheduleCount}");

        // Generate individual lecture instances
        $this->command->info('Generating individual lecture instances...');

        $generatedLectures = 0;

        foreach (LectureSchedule::all() as $schedule) {
            // Generate lectures
            $lectures = $this->generateLecturesForSchedule($schedule);
            $generatedLectures += count($lectures);
        }

        $this->command->info("Generated {$generatedLectures} individual lecture instances.");
    }

    /**
     * Generate individual lectures from a schedule.
     */
    private function generateLecturesForSchedule($schedule)
    {
        $lectures = [];

        // Get students count for setting expected students
        $course = Course::find($schedule->course_id);
        $expectedStudents = $course->students()->count();
        if ($expectedStudents === 0) {
            $expectedStudents = rand(20, 60); // Default if no students enrolled
        }

        // Convert day name to day number (0 = Sunday, 1 = Monday, etc.)
        $dayMap = [
            'Sunday' => 0,
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
        ];

        $dayNumber = $dayMap[$schedule->day_of_week];

        // Start from semester start
        $currentDate = $schedule->start_date->copy();

        // Adjust to the first occurrence of the target day
        while ($currentDate->dayOfWeek !== $dayNumber) {
            $currentDate->addDay();
        }

        // Create lectures for each occurrence of the day
        while ($currentDate->lte($schedule->end_date)) {
            $lectureDateStr = $currentDate->format('Y-m-d');

            // Create start and end times for this lecture by combining date and time
            $startTime = Carbon::parse($lectureDateStr . ' ' . $schedule->start_time);
            $endTime = Carbon::parse($lectureDateStr . ' ' . $schedule->end_time);

            // Set completion status based on whether it's in the past
            $isCompleted = $endTime->lt(Carbon::now());

            // Create the lecture
            $lecture = Lecture::create([
                'course_id' => $schedule->course_id,
                'lecturer_id' => $schedule->lecturer_id,
                'lecture_schedule_id' => $schedule->id,
                'room' => $schedule->room,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'expected_students' => $expectedStudents,
                'is_completed' => $isCompleted,
                'image_url' => $course->image_url ?? null, // Use same image as course
            ]);

            $lectures[] = $lecture;

            // Move to next week
            $currentDate->addWeek();
        }

        return $lectures;
    }
}
