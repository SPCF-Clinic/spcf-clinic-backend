<?php

namespace App\Http\Requests\MedicalHistoryField;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SwitchFormOrderRequest extends FormRequest
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
            'field_ids' => 'required|array|min:2|max:2',
            'field_ids.*' => 'exists:medical_history_fields,id'
        ];
    }
}
