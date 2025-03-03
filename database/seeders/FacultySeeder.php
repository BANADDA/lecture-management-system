<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty;

class FacultySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faculties = [
            [
                'name' => 'Faculty of Science',
                'code' => 'SCI',
                'description' => 'The Faculty of Science focuses on pure and applied sciences including mathematics, physics, chemistry, and computer science.',
                'image_url' => 'faculties/science.jpg',
            ],
            [
                'name' => 'Faculty of Business',
                'code' => 'BUS',
                'description' => 'The Faculty of Business provides education in business administration, marketing, accounting, and management.',
                'image_url' => 'faculties/business.jpg',
            ],
            [
                'name' => 'Faculty of Engineering',
                'code' => 'ENG',
                'description' => 'The Faculty of Engineering offers programs in civil, mechanical, electrical, and software engineering.',
                'image_url' => 'faculties/engineering.jpg',
            ],
            [
                'name' => 'Faculty of Health Sciences',
                'code' => 'HSC',
                'description' => 'The Faculty of Health Sciences offers programs in nursing, pharmacy, medicine, and public health.',
                'image_url' => 'faculties/health.jpg',
            ],
            [
                'name' => 'Faculty of Arts',
                'code' => 'ARTS',
                'description' => 'The Faculty of Arts covers humanities, social sciences, languages, and creative arts.',
                'image_url' => 'faculties/arts.jpg',
            ],
            [
                'name' => 'Faculty of Education',
                'code' => 'EDU',
                'description' => 'The Faculty of Education prepares students for careers in teaching and educational administration.',
                'image_url' => 'faculties/education.jpg',
            ],
        ];

        foreach ($faculties as $faculty) {
            Faculty::create($faculty);
        }

        $this->command->info('Faculties seeded successfully!');
    }
}
