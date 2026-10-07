<?php

namespace App\Http\Requests\Student;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexStudentRequest extends FormRequest
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
            'student_id' => 'sometimes|nullable|string|max:255',
            'sort_by' => 'sometimes|nullable|string|in:student_id,name,year_level,age,sex,status',
            'sort_order' => 'sometimes|nullable|string|in:asc,desc',
            'per_page' => 'sometimes|nullable|integer|min:1|max:100',
            'page' => 'sometimes|nullable|integer|min:1',
        ];
    }
}
