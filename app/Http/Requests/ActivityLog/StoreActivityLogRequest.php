<?php

namespace App\Http\Requests\ActivityLog;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreActivityLogRequest extends FormRequest
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
            'group' => ['required', 'in:AUTH,BED,CHECK-IN,INVENTORY,STUDENT_RECORD,FORM_FIELD,TIMER'],
            'action' => ['required', 'string', 'max:255']
        ];
    }
}
