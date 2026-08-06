<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalHistoryFieldResource extends JsonResource
{
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

        $options = $latestVersion?->options?->map(function ($option) {
            return $option->option_value;
        })->values()->all();

        return [
            'name' => $latestVersion?->field_name ?? $this->name,
            'type' => $type,
            'options' => $options ?? [],
            'is_required' => (bool) ($latestVersion?->is_required ?? false),
        ];
    }
}
