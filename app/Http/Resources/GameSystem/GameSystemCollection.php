<?php

namespace App\Http\Resources\GameSystem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GameSystemCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => GameSystemResource::collection($this->collection),
        ];
    }
}
