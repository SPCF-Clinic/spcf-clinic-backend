<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{
    User,
    StudentInfo
};
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::create([
            'username' => 'superadmin',
            'password' => Hash::make('superadmin'),
        ]);
        $superAdmin->assignRole('SUPER_ADMIN');

        $admins = ['admin1', 'admin2', 'admin3'];
        foreach ($admins as $admin) {
            $user = User::create([
                'username' => $admin,
                'password' => Hash::make('admin'),
            ]);
            $user->assignRole('ADMIN');
        }

        $students = ['student1', 'student2', 'student3'];
        foreach ($students as $student) {
            $studentInfo = $this->generateStudentInfo();
            $user = User::create([
                'username' => $studentInfo['student_id'],
                'password' => Hash::make('student'),
            ]);
            $user->assignRole('USER');
            $studentInfo['user_id'] = $user->id;
            $user->studentInfo()->create($studentInfo);
        }
    }

    private function generateStudentInfo() {
        $prefixes = ['0123', '0124', '0125', '0126'];
        $prefix = fake()->randomElement($prefixes);
        $lastStudentId = StudentInfo::whereNotNull('student_id')->latest()->value('student_id');
        if ($lastStudentId) {
            $lastStudentId = substr($lastStudentId, 4);
        }
        $newStudentId = $lastStudentId ? str_pad((int) $lastStudentId + 1, 6, '0', STR_PAD_LEFT) : '000001';

        $lastName = fake()->lastName();

        $yearLevel = fake()->numberBetween(0, 16);
        if ($yearLevel <= 12) {
            $educationLevel = 'BASIC_ED';
            $course = null;
            $section = 'Section-' . fake()->numberBetween(1, 10);
        } else {
            $educationLevel = 'COLLEGE';
            $department = fake()->randomElement(Department::pluck('name')->toArray());
            $course = Course::whereHas('department', function ($query) use ($department) {
                $query->where('name', $department);
            })->inRandomOrder()->value('id');
            $section = Department::where('name', $department)->value('code') . string($yearlevel*2) . fake()->randomElement(['A', 'B', 'C', 'D']);
        }

        return [
            'student_id' => $prefix . $newStudentId,
            'last_name' => $lastName,
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->lastName(),
            'birthdate' => fake()->date(),
            'gender' => fake()->randomElement(['MALE', 'FEMALE']),
            'religion' => fake()->randomElement(['Roman Catholic', 'Protestant', 'Islam', 'Other']),
            'nationality' => 'Filipino',
            'address' => fake()->address(),
            'contact_number' => fake()->phoneNumber(),
            'education_level' => $educationLevel,
            'year_level' => $yearLevel,
            'course_id' => $course,
            'section' => $section,
            'mother_name' => fake()->firstName('female') . ' ' . $lastName,
            'father_name' => fake()->firstName('male') . ' ' . $lastName,
            'guardian_name' => fake()->firstName() . ' ' . $lastName,
            'guardian_contact_number' => fake()->phoneNumber(),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_number' => fake()->phoneNumber(),
            'covid_19_vaccination' => fake()->boolean(),
        ];
    }
}
