<?php

namespace App\Http\Resources\Message;

use App\Http\Resources\Dice\DiceRollResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Message */
class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type?->value ?? $this->type,
            'content' => $this->content,
            'metadata' => $this->metadata ?? [],
            'session_id' => $this->session_id,
            'author' => new UserResource($this->whenLoaded('user')),
            'dice_roll' => new DiceRollResource($this->whenLoaded('diceRoll')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
