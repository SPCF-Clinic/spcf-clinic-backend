<?php

namespace App\Http\Requests\User;

use App\Models\{
    Department,
    Course,
    PersonalInfoField,
    MedicalHistoryField,
};
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

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
     * Every field of the given model that is actually collectible on the
     * registration form — i.e. not a non-answerable divider/section header.
     */
    private function answerableFields(string $modelClass): Collection
    {
        return $modelClass::with('latestVersion.formFieldType', 'latestVersion.options')
            ->get()
            ->filter(fn ($field) => $field->latestVersion?->formFieldType?->is_answerable)
            ->values();
    }

    /**
     * Rejects any field id in the payload that doesn't correspond to a
     * currently answerable field (unknown, deleted, or a divider).
     */
    private function knownFieldsRule(Collection $fields): \Closure
    {
        $knownIds = $fields->pluck('id')->all();

        return function ($attribute, $value, $fail) use ($knownIds) {
            if (! is_array($value)) {
                return;
            }

            $unknown = array_diff(array_keys($value), $knownIds);

            if (! empty($unknown)) {
                $fail('The '.$attribute.' contains unrecognized field(s): '.implode(', ', $unknown).'.');
            }
        };
    }

    /**
     * Build one validation rule set per field, keyed by "<group>.<field_id>"
     * so each field's presence/type rules apply to its own payload slot.
     */
    private function fieldRules(Collection $fields, string $group): array
    {
        $rules = [];

        foreach ($fields as $field) {
            $version = $field->latestVersion;
            $type = $version->formFieldType;
            $key = "{$group}.{$field->id}";

            $presence = $this->presenceRule($version, $group);
            $optionValues = $type->has_options ? $version->options->pluck('option_value')->all() : [];

            if ($type->can_select_multiple) {
                $rules[$key] = [$presence, 'array'];
                $rules["{$key}.*"] = ['string', Rule::in($optionValues)];

                continue;
            }

            $rules[$key] = match (true) {
                $type->has_options => [$presence, Rule::in($optionValues)],
                $type->name === 'Date' => [$presence, 'date'],
                default => [$presence, 'string', 'max:1000'],
            };

            // Preserve the original business rule that a chosen Course must
            // actually belong to the chosen Department — the generic
            // Rule::in(options) check above only confirms the course *code*
            // is valid somewhere, not that it matches the department.
            if ($group === 'personal_info' && $version->field_name === 'Course') {
                $rules[$key][] = $this->courseBelongsToDepartmentRule($fields);
            }
        }

        return $rules;
    }

    private function presenceRule($version, string $group): string
    {
        if (! $version->is_required) {
            return 'nullable';
        }

        if ($version->required_with_field_id) {
            return "required_if:{$group}.{$version->required_with_field_id},{$version->required_with_field_value}";
        }

        return 'required';
    }

    private function courseBelongsToDepartmentRule(Collection $personalInfoFields): \Closure
    {
        return function ($attribute, $value, $fail) use ($personalInfoFields) {
            if ($value === null) {
                return;
            }

            $departmentField = $personalInfoFields->first(
                fn ($field) => $field->latestVersion?->field_name === 'Department'
            );

            $departmentCode = $departmentField
                ? data_get($this->input('personal_info', []), $departmentField->id)
                : null;

            $department = $departmentCode ? Department::where('code', $departmentCode)->first() : null;

            if (! $department || ! Course::where('department_id', $department->id)->where('code', $value)->exists()) {
                $fail('The selected course is invalid for the specified department.');
            }
        };
    }
}