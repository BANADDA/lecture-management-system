<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Faculty;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all faculties
        $faculties = Faculty::all();

        if ($faculties->isEmpty()) {
            $this->command->error('No faculties found. Please run the FacultySeeder first.');
            return;
        }

        // Define departments by faculty code
        $departmentsByFaculty = [
            'SCI' => [
                [
                    'name' => 'Computer Science',
                    'code' => 'CS',
                    'description' => 'The Department of Computer Science focuses on programming, algorithms, data structures, and computing systems.',
                    'campus' => 'Main Campus',
                    'image_url' => 'departments/computer_science.jpg',
                ],
                [
                    'name' => 'Mathematics',
                    'code' => 'MATH',
                    'description' => 'The Department of Mathematics covers pure and applied mathematics, statistics, and mathematical modeling.',
                    'campus' => 'Main Campus',
                    'image_url' => 'departments/mathematics.jpg',
                ],
                [
                    'name' => 'Physics',
                    'code' => 'PHYS',
                    'description' => 'The Department of Physics focuses on theoretical and experimental physics, quantum mechanics, and thermodynamics.',
                    'campus' => 'Science Campus',
                    'image_url' => 'departments/physics.jpg',
                ],
                [
                    'name' => 'Chemistry',
                    'code' => 'CHEM',
                    'description' => 'The Department of Chemistry covers organic, inorganic, physical, and analytical chemistry.',
                    'campus' => 'Science Campus',
                    'image_url' => 'departments/chemistry.jpg',
                ],
            ],
            'BUS' => [
                [
                    'name' => 'Business Administration',
                    'code' => 'BA',
                    'description' => 'The Department of Business Administration provides education in management principles, organizational behavior, and strategic planning.',
                    'campus' => 'City Campus',
                    'image_url' => 'departments/business_admin.jpg',
                ],
                [
                    'name' => 'Marketing',
                    'code' => 'MKT',
                    'description' => 'The Department of Marketing focuses on market research, consumer behavior, advertising, and digital marketing.',
                    'campus' => 'City Campus',
                    'image_url' => 'departments/marketing.jpg',
                ],
                [
                    'name' => 'Accounting',
                    'code' => 'ACCT',
                    'description' => 'The Department of Accounting provides education in financial accounting, auditing, taxation, and financial management.',
                    'campus' => 'City Campus',
                    'image_url' => 'departments/accounting.jpg',
                ],
            ],
            'ENG' => [
                [
                    'name' => 'Civil Engineering',
                    'code' => 'CE',
                    'description' => 'The Department of Civil Engineering focuses on structural design, transportation systems, and water resources.',
                    'campus' => 'Engineering Campus',
                    'image_url' => 'departments/civil_eng.jpg',
                ],
                [
                    'name' => 'Electrical Engineering',
                    'code' => 'EE',
                    'description' => 'The Department of Electrical Engineering covers electronics, power systems, telecommunications, and control systems.',
                    'campus' => 'Engineering Campus',
                    'image_url' => 'departments/electrical_eng.jpg',
                ],
                [
                    'name' => 'Mechanical Engineering',
                    'code' => 'ME',
                    'description' => 'The Department of Mechanical Engineering focuses on thermodynamics, fluid mechanics, and machine design.',
                    'campus' => 'Engineering Campus',
                    'image_url' => 'departments/mechanical_eng.jpg',
                ],
                [
                    'name' => 'Software Engineering',
                    'code' => 'SE',
                    'description' => 'The Department of Software Engineering provides education in software development, testing, and maintenance.',
                    'campus' => 'Main Campus',
                    'image_url' => 'departments/software_eng.jpg',
                ],
            ],
            'HSC' => [
                [
                    'name' => 'Nursing',
                    'code' => 'NURS',
                    'description' => 'The Department of Nursing focuses on patient care, health promotion, and nursing practice.',
                    'campus' => 'Medical Campus',
                    'image_url' => 'departments/nursing.jpg',
                ],
                [
                    'name' => 'Pharmacy',
                    'code' => 'PHARM',
                    'description' => 'The Department of Pharmacy provides education in pharmaceutical sciences, drug development, and pharmacy practice.',
                    'campus' => 'Medical Campus',
                    'image_url' => 'departments/pharmacy.jpg',
                ],
            ],
            'ARTS' => [
                [
                    'name' => 'Languages',
                    'code' => 'LANG',
                    'description' => 'The Department of Languages offers programs in English, French, Spanish, and other languages.',
                    'campus' => 'Arts Campus',
                    'image_url' => 'departments/languages.jpg',
                ],
                [
                    'name' => 'History',
                    'code' => 'HIST',
                    'description' => 'The Department of History covers ancient, medieval, and modern history from around the world.',
                    'campus' => 'Arts Campus',
                    'image_url' => 'departments/history.jpg',
                ],
            ],
            'EDU' => [
                [
                    'name' => 'Teacher Education',
                    'code' => 'TE',
                    'description' => 'The Department of Teacher Education prepares students for careers in primary and secondary education.',
                    'campus' => 'Education Campus',
                    'image_url' => 'departments/teacher_edu.jpg',
                ],
                [
                    'name' => 'Educational Leadership',
                    'code' => 'EL',
                    'description' => 'The Department of Educational Leadership focuses on school administration and educational policy.',
                    'campus' => 'Education Campus',
                    'image_url' => 'departments/educational_leadership.jpg',
                ],
            ],
        ];

        // Create departments
        foreach ($faculties as $faculty) {
            $facultyCode = $faculty->code;

            if (isset($departmentsByFaculty[$facultyCode])) {
                foreach ($departmentsByFaculty[$facultyCode] as $departmentData) {
                    Department::create([
                        'faculty_id' => $faculty->id,
                        'name' => $departmentData['name'],
                        'code' => $departmentData['code'],
                        'description' => $departmentData['description'],
                        'campus' => $departmentData['campus'],
                        'image_url' => $departmentData['image_url'],
                    ]);
                }
            }
        }

        $this->command->info('Departments seeded successfully!');
    }
}
