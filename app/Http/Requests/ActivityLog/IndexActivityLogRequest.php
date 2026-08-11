<?php

namespace App\Http\Requests\ActivityLog;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexActivityLogRequest extends FormRequest
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
            'group' => ['sometimes', 'nullable', 'in:AUTH,INVENTORY,STUDENT_RECORD,CHECK-IN,BED,FORM_FIELD,TIMER'],
            'performed_by' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'per_page' => ['sometimes', 'nullable', 'integer', 'min:1'],
        ];
    }
}
