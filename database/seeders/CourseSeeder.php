<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Program;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all programs
        $programs = Program::all();

        if ($programs->isEmpty()) {
            $this->command->error('No programs found. Please run the ProgramSeeder first.');
            return;
        }

        // Find specific programs for our courses
        $bscCS = $programs->where('code', 'BSC-CS')->first();
        $bscSE = $programs->where('code', 'BSC-SE')->first();
        $bscMath = $programs->where('code', 'BSC-MATH')->first();
        $bba = $programs->where('code', 'BBA')->first();
        $bscMkt = $programs->where('code', 'BSC-MKT')->first();

        // Define courses by program
        $courses = [];

        // BSc Computer Science Courses
        if ($bscCS) {
            $courses = array_merge($courses, [
                // Year 1, Semester 1
                [
                    'program_id' => $bscCS->id,
                    'code' => 'CSC101',
                    'name' => 'Introduction to Computer Science',
                    'description' => 'An introduction to fundamental computer science concepts including algorithms, data structures, and problem-solving strategies.',
                    'year' => 1,
                    'semester' => 1,
                    'credits' => 3,
                    'image_url' => 'courses/csc101.jpg',
                ],
                [
                    'program_id' => $bscCS->id,
                    'code' => 'CSC102',
                    'name' => 'Programming Fundamentals',
                    'description' => 'Introduction to programming concepts, syntax, and problem-solving using a high-level programming language.',
                    'year' => 1,
                    'semester' => 1,
                    'credits' => 4,
                    'image_url' => 'courses/csc102.jpg',
                ],
                [
                    'program_id' => $bscCS->id,
                    'code' => 'MTH101',
                    'name' => 'Calculus I',
                    'description' => 'Introduction to differential and integral calculus of functions of one variable.',
                    'year' => 1,
                    'semester' => 1,
                    'credits' => 3,
                    'image_url' => 'courses/mth101.jpg',
                ],

                // Year 1, Semester 2
                [
                    'program_id' => $bscCS->id,
                    'code' => 'CSC103',
                    'name' => 'Object-Oriented Programming',
                    'description' => 'Introduction to object-oriented programming principles and design using languages like Java or C++.',
                    'year' => 1,
                    'semester' => 2,
                    'credits' => 4,
                    'image_url' => 'courses/csc103.jpg',
                ],
                [
                    'program_id' => $bscCS->id,
                    'code' => 'CSC104',
                    'name' => 'Discrete Mathematics',
                    'description' => 'Mathematical structures and techniques fundamental to computer science, including logic, sets, relations, and graph theory.',
                    'year' => 1,
                    'semester' => 2,
                    'credits' => 3,
                    'image_url' => 'courses/csc104.jpg',
                ],

                // Year 2, Semester 1
                [
                    'program_id' => $bscCS->id,
                    'code' => 'CSC201',
                    'name' => 'Data Structures and Algorithms',
                    'description' => 'Study of data structures and algorithms for solving computational problems, including their design, analysis, and implementation.',
                    'year' => 2,
                    'semester' => 1,
                    'credits' => 4,
                    'image_url' => 'courses/csc201.jpg',
                ],
                [
                    'program_id' => $bscCS->id,
                    'code' => 'CSC202',
                    'name' => 'Database Systems',
                    'description' => 'Introduction to database concepts, design principles, and SQL programming.',
                    'year' => 2,
                    'semester' => 1,
                    'credits' => 3,
                    'image_url' => 'courses/csc202.jpg',
                ],

                // Year 2, Semester 2
                [
                    'program_id' => $bscCS->id,
                    'code' => 'CSC203',
                    'name' => 'Computer Architecture',
                    'description' => 'Study of the structure and operation of computer systems, including processor architecture, memory hierarchy, and I/O systems.',
                    'year' => 2,
                    'semester' => 2,
                    'credits' => 3,
                    'image_url' => 'courses/csc203.jpg',
                ],
                [
                    'program_id' => $bscCS->id,
                    'code' => 'CSC204',
                    'name' => 'Operating Systems',
                    'description' => 'Study of operating system principles, components, and design, including process management, memory management, and file systems.',
                    'year' => 2,
                    'semester' => 2,
                    'credits' => 3,
                    'image_url' => 'courses/csc204.jpg',
                ],

                // Year 3, Semester 1
                [
                    'program_id' => $bscCS->id,
                    'code' => 'CSC301',
                    'name' => 'Software Engineering',
                    'description' => 'Introduction to software engineering principles, methodologies, and tools for developing and maintaining large-scale software systems.',
                    'year' => 3,
                    'semester' => 1,
                    'credits' => 3,
                    'image_url' => 'courses/csc301.jpg',
                ],
                [
                    'program_id' => $bscCS->id,
                    'code' => 'CSC302',
                    'name' => 'Artificial Intelligence',
                    'description' => 'Introduction to artificial intelligence concepts and techniques, including knowledge representation, search strategies, and machine learning.',
                    'year' => 3,
                    'semester' => 1,
                    'credits' => 3,
                    'image_url' => 'courses/csc302.jpg',
                ],

                // Year 3, Semester 2
                [
                    'program_id' => $bscCS->id,
                    'code' => 'CSC303',
                    'name' => 'Computer Networks',
                    'description' => 'Introduction to computer network concepts, architectures, protocols, and applications.',
                    'year' => 3,
                    'semester' => 2,
                    'credits' => 3,
                    'image_url' => 'courses/csc303.jpg',
                ],
                [
                    'program_id' => $bscCS->id,
                    'code' => 'CSC304',
                    'name' => 'Web Development',
                    'description' => 'Introduction to web development concepts, technologies, and practices, including front-end and back-end development.',
                    'year' => 3,
                    'semester' => 2,
                    'credits' => 3,
                    'image_url' => 'courses/csc304.jpg',
                ],

                // Year 4, Semester 1
                [
                    'program_id' => $bscCS->id,
                    'code' => 'CSC401',
                    'name' => 'Machine Learning',
                    'description' => 'Study of machine learning algorithms, techniques, and applications for data analysis and prediction.',
                    'year' => 4,
                    'semester' => 1,
                    'credits' => 3,
                    'image_url' => 'courses/csc401.jpg',
                ],
                [
                    'program_id' => $bscCS->id,
                    'code' => 'CSC402',
                    'name' => 'Capstone Project I',
                    'description' => 'First part of a year-long team project to design, implement, and evaluate a significant software system.',
                    'year' => 4,
                    'semester' => 1,
                    'credits' => 3,
                    'image_url' => 'courses/csc402.jpg',
                ],

                // Year 4, Semester 2
                [
                    'program_id' => $bscCS->id,
                    'code' => 'CSC403',
                    'name' => 'Cybersecurity',
                    'description' => 'Study of security principles, threats, vulnerabilities, and countermeasures for computer systems and networks.',
                    'year' => 4,
                    'semester' => 2,
                    'credits' => 3,
                    'image_url' => 'courses/csc403.jpg',
                ],
                [
                    'program_id' => $bscCS->id,
                    'code' => 'CSC404',
                    'name' => 'Capstone Project II',
                    'description' => 'Second part of a year-long team project to design, implement, and evaluate a significant software system.',
                    'year' => 4,
                    'semester' => 2,
                    'credits' => 3,
                    'image_url' => 'courses/csc404.jpg',
                ],
            ]);
        }

        // BSc Software Engineering Courses
        if ($bscSE) {
            $courses = array_merge($courses, [
                // Just a few examples
                [
                    'program_id' => $bscSE->id,
                    'code' => 'SE101',
                    'name' => 'Introduction to Software Engineering',
                    'description' => 'An introduction to fundamental software engineering concepts, methodologies, and practices.',
                    'year' => 1,
                    'semester' => 1,
                    'credits' => 3,
                    'image_url' => 'courses/se101.jpg',
                ],
                [
                    'program_id' => $bscSE->id,
                    'code' => 'SE201',
                    'name' => 'Software Design and Architecture',
                    'description' => 'Study of software design principles, patterns, and architectural styles.',
                    'year' => 2,
                    'semester' => 1,
                    'credits' => 3,
                    'image_url' => 'courses/se201.jpg',
                ],
            ]);
        }

        // BBA Courses
        if ($bba) {
            $courses = array_merge($courses, [
                [
                    'program_id' => $bba->id,
                    'code' => 'BUS101',
                    'name' => 'Introduction to Business',
                    'description' => 'An overview of business principles, concepts, and practices.',
                    'year' => 1,
                    'semester' => 1,
                    'credits' => 3,
                    'image_url' => 'courses/bus101.jpg',
                ],
                [
                    'program_id' => $bba->id,
                    'code' => 'MKT101',
                    'name' => 'Principles of Marketing',
                    'description' => 'Introduction to marketing concepts, strategies, and practices.',
                    'year' => 1,
                    'semester' => 2,
                    'credits' => 3,
                    'image_url' => 'courses/mkt101.jpg',
                ],
                [
                    'program_id' => $bba->id,
                    'code' => 'ACC101',
                    'name' => 'Principles of Accounting',
                    'description' => 'Introduction to accounting principles, concepts, and practices.',
                    'year' => 1,
                    'semester' => 2,
                    'credits' => 3,
                    'image_url' => 'courses/acc101.jpg',
                ],
            ]);
        }

        // BSc Marketing Courses
        if ($bscMkt) {
            $courses = array_merge($courses, [
                [
                    'program_id' => $bscMkt->id,
                    'code' => 'MKT201',
                    'name' => 'Consumer Behavior',
                    'description' => 'Study of consumer decision-making processes and factors influencing purchasing behaviors.',
                    'year' => 2,
                    'semester' => 1,
                    'credits' => 3,
                    'image_url' => 'courses/mkt201.jpg',
                ],
                [
                    'program_id' => $bscMkt->id,
                    'code' => 'MKT202',
                    'name' => 'Digital Marketing',
                    'description' => 'Study of digital marketing strategies, channels, and tools.',
                    'year' => 2,
                    'semester' => 2,
                    'credits' => 3,
                    'image_url' => 'courses/mkt202.jpg',
                ],
            ]);
        }

        // Create all courses
        foreach ($courses as $courseData) {
            Course::create($courseData);
        }

        $this->command->info('Courses seeded successfully! Total courses: ' . count($courses));
    }
}
