<?php

namespace App\Http\Requests\Auth;

use App\Traits\BuildsFieldAnswerRules;
use App\Models\{
    PersonalInfoField,
    MedicalHistoryField,
};
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Registration payload shape:
 *
 * {
 *   "student_id": "0123000123",
 *   "password": "...",
 *   "personal_info": { "<personal_info_field_id>": "<value>", ... },
 *   "medical_history": { "<medical_history_field_id>": "<value>", ... }
 * }
 *
 * `value` is a plain scalar for single-answer fields (Short/Long Text, Date,
 * Dropdown, Radio) and an array of strings for Checkbox (multi-select)
 * fields. Divider fields are section headers, not answerable, and must not
 * appear as keys in either payload.
 */
class RegisterRequest extends FormRequest
{
    use BuildsFieldAnswerRules;

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
        $personalInfoFields = $this->answerableFields(PersonalInfoField::class);
        $medicalHistoryFields = $this->answerableFields(MedicalHistoryField::class);

        $rules = [
            'student_id' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:8',
            'personal_info' => ['required', 'array', $this->knownFieldsRule($personalInfoFields)],
            'medical_history' => ['required', 'array', $this->knownFieldsRule($medicalHistoryFields)],
        ];

        return array_merge(
            $rules,
            $this->fieldRules($personalInfoFields, 'personal_info'),
            $this->fieldRules($medicalHistoryFields, 'medical_history'),
        );
    }

    /**
     * Everything is required unless the field itself is marked optional, in
     * which case it's conditionally required (required_if) when it depends
     * on another field, or simply nullable otherwise.
     */
    protected function fieldPresenceRule($version, string $group): string
    {
        if (! $version->is_required) {
            return 'nullable';
        }

        if ($version->required_with_field_id) {
            return "required_if:{$group}.{$version->required_with_field_id},{$version->required_with_field_value}";
        }

        return 'required';
    }
}