<?php

namespace App\Services\Campaign;

use App\Models\Campaign;
use App\Models\CampaignReference;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CampaignReferenceService
{
    public function createReference(User $actor, Campaign $campaign, array $data): CampaignReference
    {
        return DB::transaction(function () use ($actor, $campaign, $data): CampaignReference {
            $reference = $campaign->references()->create([
                ...Arr::only($data, ['title', 'type', 'content', 'external_url']),
                'sort_order' => $data['sort_order'] ?? $this->nextSortOrder($campaign),
                'created_by' => $actor->getKey(),
            ]);

            return $reference->load('creator');
        });
    }

    public function updateReference(CampaignReference $reference, array $data): CampaignReference
    {
        return DB::transaction(function () use ($reference, $data): CampaignReference {
            $reference->fill(Arr::only($data, ['title', 'type', 'content', 'external_url', 'sort_order']));
            $reference->save();

            return $reference->refresh()->load('creator');
        });
    }

    public function deleteReference(CampaignReference $reference): void
    {
        DB::transaction(function () use ($reference): void {
            $reference->delete();
        });
    }

    protected function nextSortOrder(Campaign $campaign): int
    {
        return ((int) $campaign->references()->max('sort_order')) + 1;
    }
}
