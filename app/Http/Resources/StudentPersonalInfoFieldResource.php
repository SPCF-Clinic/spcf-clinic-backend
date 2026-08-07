<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Same shape as PersonalInfoFieldResource, plus the given student's answer
 * (looked up from a `personal_info_field_id => UserPersonalInfo` map) merged
 * into each field, including nested additional_fields.
 */
class StudentPersonalInfoFieldResource extends JsonResource
{
    protected Collection $answers;

    public function __construct($resource, ?Collection $answers = null)
    {
        parent::__construct($resource);
        $this->answers = $answers ?? collect();
    }

    /**
     * @param  \Illuminate\Support\Collection  $resource
     * @param  \Illuminate\Support\Collection  $answers  personal_info_field_id => UserPersonalInfo
     */
    public static function collection($resource, ?Collection $answers = null)
    {
        $answers = $answers ?? collect();

        return $resource->map(fn ($field) => new static($field, $answers))->values();
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $latestVersion = $this->latestVersion;

        $type = $latestVersion?->formFieldType ? [
            'name' => $latestVersion->formFieldType->name,
            'is_answerable' => (bool) $latestVersion->formFieldType->is_answerable,
            'has_options' => (bool) $latestVersion->formFieldType->has_options,
            'can_select_multiple' => (bool) $latestVersion->formFieldType->can_select_multiple,
        ] : null;

        $additionalFields = $this->requiredByFields ? $this->requiredByFields->groupBy('personal_info_field_id')->map(function ($fields) {
            $field = $fields->sortByDesc('version_number')->first();

            $type = $field?->formFieldType ? [
                'name' => $field->formFieldType->name,
                'is_answerable' => (bool) $field->formFieldType->is_answerable,
                'has_options' => (bool) $field->formFieldType->has_options,
                'can_select_multiple' => (bool) $field->formFieldType->can_select_multiple,
            ] : null;

            return [
                'id' => $field->personal_info_field_id,
                'name' => $field?->field_name,
                'value' => $this->valueFor($field->personal_info_field_id, $type),
            ];
        })->values()->all() : [];

        return [
            'id' => $this->id,
            'name' => $latestVersion?->field_name ?? $this->name,
            'form_order' => $latestVersion?->form_order,
            'value' => $this->valueFor($this->id, $type),
            'additional_fields' => $additionalFields,
        ];
    }

    /**
     * The student's answer for the given field id, or null if unanswered.
     * Multi-select (Checkbox) answers are stored JSON-encoded and are
     * decoded back into an array here.
     */
    private function valueFor(int $fieldId, ?array $type)
    {
        $answer = $this->answers->get($fieldId);

        if (! $answer) {
            return null;
        }

        if ($type && $type['can_select_multiple']) {
            $decoded = json_decode($answer->value, true);

            return is_array($decoded) ? $decoded : [$answer->value];
        }

        return $answer->value;
    }
}