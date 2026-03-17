<?php

namespace App\Http\Resources\Campaign;

use App\Http\Resources\CampaignReference\CampaignReferenceResource;
use App\Http\Resources\GameSystem\GameSystemResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Campaign */
class CampaignResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'synopsis' => $this->synopsis,
            'description' => $this->description,
            'rules_summary' => $this->rules_summary,
            'max_players' => $this->max_players,
            'visibility' => $this->visibility?->value ?? $this->visibility,
            'status' => $this->status?->value ?? $this->status,
            'language' => $this->language,
            'timezone' => $this->timezone,
            'frequency_label' => $this->frequency_label,
            'next_session_at' => $this->next_session_at?->toIso8601String(),
            'owner' => new UserResource($this->whenLoaded('owner')),
            'game_system' => new GameSystemResource($this->whenLoaded('gameSystem')),
            'references' => CampaignReferenceResource::collection($this->whenLoaded('references')),
            'members_count' => $this->whenCounted('members'),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
