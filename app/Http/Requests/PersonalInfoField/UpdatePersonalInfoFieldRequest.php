<?php

namespace App\Http\Requests\PersonalInfoField;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\FormFieldType;
use Illuminate\Validation\Rule;

class UpdatePersonalInfoFieldRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'max:255', Rule::exists(FormFieldType::class, 'name')],
            'options' => ['sometimes', 'nullable', 'array', function ($attribute, $value, $fail) {
                $type = FormFieldType::where('name', $this->type)->first();
                if (!$type) {
                    $fail('The selected type is invalid.');
                    return;
                }
                if ($type->has_options && (!is_array($value) || empty($value))) {
                    $fail('The options field is required when the type has options.');
                }
                if (!$type->has_options && !empty($value)) {
                    $fail('The options field must be empty when the type does not have options.');
                }
            }],
            'options.*' => 'required_with:options|string|max:255',
            'is_required' => 'sometimes|boolean',
            'required_with_field_id' => ['sometimes', 'nullable', 'integer', 'exists:personal_info_fields,id', function ($attribute, $value, $fail) {
                if ($this->is_required && $value) {
                    $fail('The required_with_field_id field must be null when is_required is true.');
                }
            }],
            'required_with_field_value' => ['sometimes', 'nullable', 'string', 'max:255', function ($attribute, $value, $fail) {
                if ($this->is_required && $value) {
                    $fail('The required_with_field_value field must be null when is_required is true.');
                }
            }],
        ];
    }
}
