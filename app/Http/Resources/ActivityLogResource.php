<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
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
            'group' => $this->group,
            'action' => $this->action,
            'user' => $this->performedFor ? ($this->performedFor->hasName() ? $this->performedFor->getFullNameAttribute() : $this->performedFor->username) : ($this->performedBy->hasName() ? $this->performedBy->getFullNameAttribute() : $this->performedBy->username),
            'created_at' => $this->created_at,
        ];
    }
}
