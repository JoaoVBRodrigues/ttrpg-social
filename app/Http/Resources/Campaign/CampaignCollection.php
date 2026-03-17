<?php

namespace App\Http\Resources\Campaign;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CampaignCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => CampaignResource::collection($this->collection),
            'meta' => [
                'current_page' => $this->resource->currentPage(),
                'per_page' => $this->resource->perPage(),
                'total' => $this->resource->total(),
                'filters' => [
                    'search' => $request->query('search'),
                    'status' => $request->query('status'),
                    'system' => $request->query('system'),
                    'language' => $request->query('language'),
                ],
            ],
            'links' => [
                'next' => $this->resource->nextPageUrl(),
                'prev' => $this->resource->previousPageUrl(),
            ],
        ];
    }
}
