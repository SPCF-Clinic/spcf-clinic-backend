<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FormFieldTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_answerable' => (bool) $this->is_answerable,
            'has_options' => (bool) $this->has_options,
            'can_select_multiple' => (bool) $this->can_select_multiple,
        ];
    }
}