<?php

namespace App\Http\Requests\MedicalHistoryField;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\MedicalHistoryFieldVersion;

class ReorderFormRequest extends FormRequest
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
            'field_id' => 'required|integer|exists:medical_history_fields,id',
            'target_form_order' => ['required', 'integer', 'min:1', function ($attribute, $value, $fail) {
                $maxFormOrder = MedicalHistoryFieldVersion::max('form_order');
                if ($value > $maxFormOrder) {
                    $fail("The {$attribute} must not be greater than {$maxFormOrder}.");
                }
            }],
            'form_version' => 'required|string',
        ];
    }
}