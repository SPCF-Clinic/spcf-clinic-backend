<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\{
    Department,
    Course,
};
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => 'required|string|max:255|unique:student_infos',
            'password' => 'required|string|min:8',
            'student_info.last_name' => 'required|string|max:255',
            'student_info.first_name' => 'required|string|max:255',
            'student_info.middle_name' => 'nullable|string|max:255',
            'student_info.birthdate' => 'required|date',
            'student_info.gender' => 'required|string|in:MALE,FEMALE',
            'student_info.religion' => 'required|string|max:255',
            'student_info.nationality' => 'required|string|max:255',
            'student_info.address' => 'required|string|max:255',
            'student_info.contact_number' => 'required|string|max:255',
            'student_info.education_level' => 'required|string|in:BASIC_ED,COLLEGE',
            'student_info.year_level' => ['required', 'integer', 'min:0', function ($attribute, $value, $fail) {
                if ($this->student_info['education_level'] === 'BASIC_ED' && $value > 12) {
                    $fail('The year level must be between 0 and 12 for BASIC_ED.');
                } elseif ($this->student_info['education_level'] === 'COLLEGE' && $value < 13) {
                    $fail('The year level must be 13 or higher for COLLEGE.');
                }
            }],
            'student_info.department' => ['required_if:student_info.education_level,COLLEGE','string','max:255','nullable',Rule::exists(Department::class, 'name')],
            'student_info.course' => ['required_if:student_info.education_level,COLLEGE','string','max:255','nullable',function ($attribute, $value, $fail) {
                if ($this->student_info['education_level'] === 'COLLEGE') {
                    $department = Department::where('name', $this->student_info['department'])->first();
                    if (!$department || !Course::where('department_id', $department->id)->where('name', $value)->exists()) {
                        $fail('The selected course is invalid for the specified department.');
                    }
                }
            }],
            'student_info.section' => 'required|string|max:255',
            'student_info.mother_name' => 'required|string|max:255',
            'student_info.father_name' => 'required|string|max:255',
            'student_info.guardian_name' => 'required|string|max:255',
            'student_info.guardian_contact_number' => 'required|string|max:255',
            'student_info.emergency_contact_name' => 'required|string|max:255',
            'student_info.emergency_contact_number' => 'required|string|max:255',
            'student_info.covid_19_vaccination' => 'required|boolean',
        ];
    }
}
