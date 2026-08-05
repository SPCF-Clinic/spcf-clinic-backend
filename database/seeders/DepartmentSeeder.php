<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Course;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //'CCIS', 'COE', 'CON', 'COC', 'COB', 'CHTM', 'CASSED'
        $departments = [
            [
                'code' => 'CCIS',
                'name' => 'College of Computing and Information Sciences',
                'courses' => [
                    ['code' => 'BSIT', 'name' => 'Bachelor of Science in Information Technology'],
                    ['code' => 'BSCS', 'name' => 'Bachelor of Science in Computer Science'],
                    ['code' => 'BSIS', 'name' => 'Bachelor of Science in Information Systems'],
                ]
            ],
            [
                'code' => 'COE',
                'name' => 'College of Engineering',
                'courses' => [
                    ['code' => 'BSCE', 'name' => 'Bachelor of Science in Computer Engineering'],
                    ['code' => 'BSECE', 'name' => 'Bachelor of Science in Electronics and Communications Engineering'],
                ]
            ],
            [
                'code' => 'CON',
                'name' => 'College of Nursing',
                'courses' => [
                    ['code' => 'BSN', 'name' => 'Bachelor of Science in Nursing'],
                ]
            ],
            [
                'code' => 'COC',
                'name' => 'College of Criminology',
                'courses' => [
                    ['code' => 'BSC', 'name' => 'Bachelor of Science in Criminology'],
                ]
            ],
            [
                'code' => 'COB',
                'name' => 'College of Business',
                'courses' => [
                    ['code' => 'BSBA', 'name' => 'Bachelor of Science in Business Administration'],
                    ['code' => 'BSA', 'name' => 'Bachelor of Science in Accountancy'],
                    ['code' => 'BSCA', 'name' => 'Bachelor of Science in Customs Administration'],
                    ['code' => 'BSREM', 'name' => 'Bachelor of Science in Real Estate Management']
                ]
            ],
            [
                'code' => 'CHTM',
                'name' => 'College of Hospitality and Tourism Management',
                'courses' => [
                    ['code' => 'BSHM', 'name' => 'Bachelor of Science in Hospitality Management'],
                    ['code' => 'BST', 'name' => 'Bachelor of Science in Tourism'],
                ]
            ],
            [
                'code' => 'CASSED',
                'name' => 'College of Arts, Social Sciences, and Education',
                'courses' => [
                    ['code' => 'BEE', 'name' => 'Bachelor of Elementary Education'],
                    ['code' => 'BSE', 'name' => 'Bachelor of Secondary Education'],
                    ['code' => 'BAC', 'name' => 'Bachelor of Arts in Communication'],
                    ['code' => 'BSSW', 'name' => 'Bachelor of Science in Social Work'],
                ]
            ]
        ];

        foreach ($departments as $departmentData) {
            $department = Department::create([
                'code' => $departmentData['code'],
                'name' => $departmentData['name'],
            ]);

            foreach ($departmentData['courses'] as $courseData) {
                Course::create([
                    'department_id' => $department->id,
                    'code' => $courseData['code'],
                    'name' => $courseData['name'],
                ]);
            }
        }
    }
}
