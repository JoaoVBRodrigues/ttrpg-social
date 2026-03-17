<?php

namespace App\Http\Resources\Dice;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\DiceRoll */
class DiceRollResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'expression' => $this->expression,
            'normalized_expression' => $this->normalized_expression,
            'dice_results' => $this->dice_results,
            'modifiers' => $this->modifiers,
            'total' => $this->total,
            'roll_mode' => $this->roll_mode?->value ?? $this->roll_mode,
            'rolled_at' => $this->rolled_at?->toIso8601String(),
        ];
    }
}
