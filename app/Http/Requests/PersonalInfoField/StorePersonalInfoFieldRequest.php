<?php

namespace App\Http\Requests\PersonalInfoField;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\FormFieldType;
use Illuminate\Validation\Rule;
use App\Models\PersonalInfoField;
use App\Models\PersonalInfoFieldVersion;

class StorePersonalInfoFieldRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'type' => ['required', 'string', 'max:255', Rule::exists(FormFieldType::class, 'name')],
            'options' => ['nullable', 'array', function ($attribute, $value, $fail) {
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
            'is_required' => 'required|boolean',
            'required_with_field_id' => ['nullable', 'integer', 'exists:personal_info_fields,id', function ($attribute, $value, $fail) {
                if ($this->is_required && $value) {
                    $fail('The required_with_field_id field must be null when is_required is true.');
                }
                $requiredWithField = PersonalInfoField::find($value);
                if ($requiredWithField && $requiredWithField->latestVersion->required_with_field_id) {
                    $fail('The selected required_with_field_id field is an additional field and cannot be required by another field.');
                }
            }],
            'required_with_field_value' => ['nullable', 'string', 'max:255', function ($attribute, $value, $fail) {
                if ($this->is_required && $value) {
                    $fail('The required_with_field_value field must be null when is_required is true.');
                }
                $requiredWithField = PersonalInfoField::find($this->required_with_field_id);
                if ($requiredWithField && $requiredWithField->latestVersion->required_with_field_id) {
                    $fail('The selected required_with_field_id field is an additional field and cannot have a required_with_field_value.');
                }
            }],
            'form_order' => ['required', 'integer', 'unique:personal_info_field_versions,form_order', function ($attribute, $value, $fail) {
                $maxFormOrder = PersonalInfoFieldVersion::max('form_order');
                if ($value < 1 || $value > $maxFormOrder + 1) {
                    $fail('The form_order must be between 1 and ' . ($maxFormOrder + 1) . '.');
                }
            }],
            'description_text' => 'sometimes|nullable|string',
        ];
    }
}
