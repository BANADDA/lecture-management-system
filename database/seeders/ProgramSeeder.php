<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\Department;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all departments
        $departments = Department::all();

        if ($departments->isEmpty()) {
            $this->command->error('No departments found. Please run the DepartmentSeeder first.');
            return;
        }

        // Define programs by department code
        $programsByDepartment = [
            'CS' => [
                [
                    'name' => 'Bachelor of Science in Computer Science',
                    'code' => 'BSC-CS',
                    'duration_years' => 4,
                    'description' => 'A comprehensive program covering programming, algorithms, data structures, and software development.',
                    'image_url' => 'programs/bsc_cs.jpg',
                ],
                [
                    'name' => 'Bachelor of Science in Data Science',
                    'code' => 'BSC-DS',
                    'duration_years' => 4,
                    'description' => 'A program focusing on data analysis, machine learning, and statistical techniques.',
                    'image_url' => 'programs/bsc_ds.jpg',
                ],
                [
                    'name' => 'Master of Science in Computer Science',
                    'code' => 'MSC-CS',
                    'duration_years' => 2,
                    'description' => 'An advanced program covering advanced algorithms, artificial intelligence, and software engineering.',
                    'image_url' => 'programs/msc_cs.jpg',
                ],
            ],
            'SE' => [
                [
                    'name' => 'Bachelor of Science in Software Engineering',
                    'code' => 'BSC-SE',
                    'duration_years' => 4,
                    'description' => 'A program focusing on software development methodologies, testing, and project management.',
                    'image_url' => 'programs/bsc_se.jpg',
                ],
                [
                    'name' => 'Master of Science in Software Engineering',
                    'code' => 'MSC-SE',
                    'duration_years' => 2,
                    'description' => 'An advanced program covering software architecture, quality assurance, and agile methodologies.',
                    'image_url' => 'programs/msc_se.jpg',
                ],
            ],
            'MATH' => [
                [
                    'name' => 'Bachelor of Science in Mathematics',
                    'code' => 'BSC-MATH',
                    'duration_years' => 3,
                    'description' => 'A program covering pure and applied mathematics, including calculus, algebra, and analysis.',
                    'image_url' => 'programs/bsc_math.jpg',
                ],
                [
                    'name' => 'Bachelor of Science in Statistics',
                    'code' => 'BSC-STAT',
                    'duration_years' => 3,
                    'description' => 'A program focusing on statistical theory, data analysis, and probability.',
                    'image_url' => 'programs/bsc_stat.jpg',
                ],
            ],
            'BA' => [
                [
                    'name' => 'Bachelor of Business Administration',
                    'code' => 'BBA',
                    'duration_years' => 4,
                    'description' => 'A comprehensive program covering management, finance, marketing, and organizational behavior.',
                    'image_url' => 'programs/bba.jpg',
                ],
                [
                    'name' => 'Master of Business Administration',
                    'code' => 'MBA',
                    'duration_years' => 2,
                    'description' => 'An advanced program covering strategic management, leadership, and business analytics.',
                    'image_url' => 'programs/mba.jpg',
                ],
            ],
            'MKT' => [
                [
                    'name' => 'Bachelor of Science in Marketing',
                    'code' => 'BSC-MKT',
                    'duration_years' => 4,
                    'description' => 'A program focusing on market research, consumer behavior, advertising, and digital marketing.',
                    'image_url' => 'programs/bsc_mkt.jpg',
                ],
            ],
            'CE' => [
                [
                    'name' => 'Bachelor of Engineering in Civil Engineering',
                    'code' => 'BE-CE',
                    'duration_years' => 4,
                    'description' => 'A program covering structural design, construction materials, and infrastructure development.',
                    'image_url' => 'programs/be_ce.jpg',
                ],
            ],
            'EE' => [
                [
                    'name' => 'Bachelor of Engineering in Electrical Engineering',
                    'code' => 'BE-EE',
                    'duration_years' => 4,
                    'description' => 'A program covering electronics, power systems, and control systems.',
                    'image_url' => 'programs/be_ee.jpg',
                ],
            ],
            'NURS' => [
                [
                    'name' => 'Bachelor of Science in Nursing',
                    'code' => 'BSC-NURS',
                    'duration_years' => 4,
                    'description' => 'A program preparing students for nursing practice with a focus on patient care and health promotion.',
                    'image_url' => 'programs/bsc_nurs.jpg',
                ],
            ],
            'TE' => [
                [
                    'name' => 'Bachelor of Education',
                    'code' => 'BED',
                    'duration_years' => 4,
                    'description' => 'A program preparing students for careers in teaching at primary and secondary levels.',
                    'image_url' => 'programs/bed.jpg',
                ],
            ],
        ];

        // Create programs
        foreach ($departments as $department) {
            $departmentCode = $department->code;

            if (isset($programsByDepartment[$departmentCode])) {
                foreach ($programsByDepartment[$departmentCode] as $programData) {
                    Program::create([
                        'department_id' => $department->id,
                        'name' => $programData['name'],
                        'code' => $programData['code'],
                        'duration_years' => $programData['duration_years'],
                        'description' => $programData['description'],
                        'image_url' => $programData['image_url'],
                    ]);
                }
            }
        }

        $this->command->info('Programs seeded successfully!');
    }
}
