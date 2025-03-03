<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\Department;
use App\Models\Program;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'admin',
            'is_active' => true,
            'remember_token' => Str::random(10),
        ]);

        $this->command->info('Admin user created successfully!');

        // Get departments
        $departments = Department::all();

        if ($departments->isEmpty()) {
            $this->command->error('No departments found. Please run the DepartmentSeeder first.');
            return;
        }

        // Create lecturers
        $this->createLecturers($departments);

        // Get programs
        $programs = Program::all();

        if ($programs->isEmpty()) {
            $this->command->error('No programs found. Please run the ProgramSeeder first.');
            return;
        }

        // Create students
        $this->createStudents($programs);

        $this->command->info('Users seeded successfully!');
    }

    /**
     * Create lecturer accounts.
     */
    private function createLecturers($departments)
    {
        // First create departmental heads
        foreach ($departments as $index => $department) {
            $user = User::create([
                'name' => "HOD {$department->name}",
                'email' => "hod.{$department->code}@example.com",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'lecturer',
                'is_active' => true,
                'remember_token' => Str::random(10),
            ]);

            Lecturer::create([
                'user_id' => $user->id,
                'staff_id' => 'L' . str_pad($department->id, 3, '0', STR_PAD_LEFT),
                'first_name' => 'Head',
                'last_name' => $department->name,
                'email' => $user->email,
                'phone' => '+1234567' . str_pad($index, 3, '0', STR_PAD_LEFT),
                'department_id' => $department->id,
                'office_location' => "Room {$department->id}01",
                'office_hours' => json_encode([
                    'Monday' => '09:00-12:00',
                    'Wednesday' => '14:00-16:00',
                    'Friday' => '10:00-12:00',
                ]),
                'status' => 'active',
            ]);
        }

        $this->command->info('Department heads created successfully!');

        // Create regular lecturers (3 per department)
        foreach ($departments as $deptIndex => $department) {
            for ($i = 1; $i <= 3; $i++) {
                $index = ($deptIndex * 3) + $i;
                $firstName = $this->getRandomFirstName();
                $lastName = $this->getRandomLastName();

                $user = User::create([
                    'name' => "{$firstName} {$lastName}",
                    'email' => "lecturer{$index}@example.com",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'role' => 'lecturer',
                    'is_active' => true,
                    'remember_token' => Str::random(10),
                ]);

                Lecturer::create([
                    'user_id' => $user->id,
                    'staff_id' => 'L' . str_pad(100 + $index, 3, '0', STR_PAD_LEFT),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $user->email,
                    'phone' => '+9876543' . str_pad($index, 3, '0', STR_PAD_LEFT),
                    'department_id' => $department->id,
                    'office_location' => "Room " . (200 + $index),
                    'office_hours' => json_encode([
                        'Monday' => '10:00-12:00',
                        'Thursday' => '14:00-16:00',
                    ]),
                    'status' => 'active',
                ]);
            }
        }

        $this->command->info('Regular lecturers created successfully!');
    }

    /**
     * Create student accounts.
     */
    private function createStudents($programs)
    {
        // Create 20 students per program
        foreach ($programs as $progIndex => $program) {
            // Calculate appropriate year and semester
            // Assuming 2 semesters per year
            $maxYear = min($program->duration_years, 4); // Cap at 4 years

            for ($i = 1; $i <= 20; $i++) {
                $index = ($progIndex * 20) + $i;
                $year = rand(1, $maxYear);
                $semester = rand(1, 2);

                $firstName = $this->getRandomFirstName();
                $lastName = $this->getRandomLastName();

                // Create student ID like MMU/2023/001
                $studentIdYear = 2020 + $year;
                $studentId = "MMU/{$studentIdYear}/" . str_pad($index, 3, '0', STR_PAD_LEFT);

                $user = User::create([
                    'name' => "{$firstName} {$lastName}",
                    'email' => "student{$index}@example.com",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'role' => 'student',
                    'is_active' => true,
                    'remember_token' => Str::random(10),
                ]);

                Student::create([
                    'user_id' => $user->id,
                    'student_id' => $studentId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $user->email,
                    'phone' => '+1122334' . str_pad($index, 3, '0', STR_PAD_LEFT),
                    'program_id' => $program->id,
                    'current_year' => $year,
                    'current_semester' => $semester,
                    'status' => 'active',
                ]);
            }
        }

        $this->command->info('Students created successfully!');
    }

    /**
     * Get a random first name.
     */
    private function getRandomFirstName()
    {
        $names = [
            'John', 'Jane', 'James', 'Emily', 'Michael', 'Sarah', 'David', 'Emma',
            'Robert', 'Olivia', 'William', 'Ava', 'Richard', 'Sophia', 'Joseph', 'Isabella',
            'Thomas', 'Mia', 'Charles', 'Charlotte', 'Daniel', 'Amelia', 'Matthew', 'Harper',
            'Anthony', 'Evelyn', 'Mark', 'Abigail', 'Donald', 'Elizabeth', 'Steven', 'Sofia',
            'Andrew', 'Victoria', 'Edward', 'Camila', 'Brian', 'Aria', 'George', 'Grace',
            'Ronald', 'Ella', 'Kevin', 'Eleanor', 'Jason', 'Hannah', 'Timothy', 'Scarlett',
            'Jose', 'Zoey', 'Larry', 'Lily', 'Jeffrey', 'Lillian', 'Frank', 'Natalie',
            'Scott', 'Chloe', 'Eric', 'Layla', 'Stephen', 'Brooklyn', 'Jacob', 'Zoe',
            'Benjamin', 'Penelope', 'Aaron', 'Riley', 'Nicholas', 'Leah', 'Alexander', 'Aubrey',
            'Kenneth', 'Savannah', 'Tyler', 'Madelyn', 'Xavier', 'Maya', 'Nathan', 'Nora'
        ];

        return $names[array_rand($names)];
    }

    /**
     * Get a random last name.
     */
    private function getRandomLastName()
    {
        $names = [
            'Smith', 'Johnson', 'Williams', 'Jones', 'Brown', 'Davis', 'Miller', 'Wilson',
            'Moore', 'Taylor', 'Anderson', 'Thomas', 'Jackson', 'White', 'Harris', 'Martin',
            'Thompson', 'Garcia', 'Martinez', 'Robinson', 'Clark', 'Rodriguez', 'Lewis', 'Lee',
            'Walker', 'Hall', 'Allen', 'Young', 'Hernandez', 'King', 'Wright', 'Lopez',
            'Hill', 'Scott', 'Green', 'Adams', 'Baker', 'Gonzalez', 'Nelson', 'Carter',
            'Mitchell', 'Perez', 'Roberts', 'Turner', 'Phillips', 'Campbell', 'Parker', 'Evans',
            'Edwards', 'Collins', 'Stewart', 'Sanchez', 'Morris', 'Rogers', 'Reed', 'Cook',
            'Morgan', 'Bell', 'Murphy', 'Bailey', 'Rivera', 'Cooper', 'Richardson', 'Cox',
            'Howard', 'Ward', 'Torres', 'Peterson', 'Gray', 'Ramirez', 'James', 'Watson',
            'Brooks', 'Kelly', 'Sanders', 'Price', 'Bennett', 'Wood', 'Barnes', 'Ross'
        ];

        return $names[array_rand($names)];
    }
}
