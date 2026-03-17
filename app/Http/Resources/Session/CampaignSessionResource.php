<?php

namespace App\Http\Resources\Session;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\CampaignSession */
class CampaignSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'starts_at' => $this->starts_at?->toIso8601String(),
            'ends_at' => $this->ends_at?->toIso8601String(),
            'timezone' => $this->timezone,
            'status' => $this->status?->value ?? $this->status,
            'cancellation_reason' => $this->cancellation_reason,
            'attendance_count' => $this->whenLoaded('attendances', fn () => $this->attendances->count()),
        ];
    }
}
