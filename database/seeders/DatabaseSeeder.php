<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call seeders in the correct order to respect foreign key relationships
        $this->call([
            FacultySeeder::class,        // 1. Create faculties first
            DepartmentSeeder::class,     // 2. Create departments (requires faculties)
            ProgramSeeder::class,        // 3. Create programs (requires departments)
            CourseSeeder::class,         // 4. Create courses (requires programs)
            UsersSeeder::class,          // 5. Create users, lecturers, and students
            LectureScheduleSeeder::class,// 6. Create lecture schedules (requires courses and lecturers)
            ClassRepresentativeSeeder::class, // 7. Create class representatives (requires students, courses, and lecturers)
        ]);
    }
}
