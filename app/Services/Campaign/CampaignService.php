<?php

namespace App\Services\Campaign;

use App\Enums\CampaignMemberRole;
use App\Enums\CampaignMemberStatus;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CampaignService
{
    public function queryPublicCampaigns(array $filters = []): Builder
    {
        return Campaign::query()
            ->with(['owner', 'gameSystem'])
            ->withCount('members')
            ->where('visibility', 'public')
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $inner) use ($search): void {
                    $inner->where('title', 'like', '%'.$search.'%')
                        ->orWhere('synopsis', 'like', '%'.$search.'%');
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['system'] ?? null, fn (Builder $query, string $system) => $query->whereHas('gameSystem', fn (Builder $systemQuery) => $systemQuery->where('slug', $system)))
            ->when($filters['language'] ?? null, fn (Builder $query, string $language) => $query->where('language', $language))
            ->when(($filters['open_only'] ?? false), fn (Builder $query) => $query->where('status', CampaignStatus::OPEN->value))
            ->latest();
    }

    public function paginatePublicCampaigns(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        return $this->queryPublicCampaigns($filters)->paginate($perPage)->withQueryString();
    }

    public function createCampaign(User $owner, array $data): Campaign
    {
        return DB::transaction(function () use ($owner, $data): Campaign {
            $campaign = Campaign::query()->create([
                ...Arr::only($data, [
                    'game_system_id',
                    'title',
                    'synopsis',
                    'description',
                    'rules_summary',
                    'max_players',
                    'visibility',
                    'language',
                    'timezone',
                    'frequency_label',
                ]),
                'owner_id' => $owner->getKey(),
                'slug' => $this->generateUniqueSlug($data['title']),
                'status' => $data['status'] ?? CampaignStatus::OPEN->value,
            ]);

            $campaign->members()->create([
                'user_id' => $owner->getKey(),
                'role' => CampaignMemberRole::GM,
                'status' => CampaignMemberStatus::ACTIVE,
                'joined_at' => now(),
            ]);

            return $campaign->load(['owner', 'gameSystem'])->loadCount('members');
        });
    }

    public function updateCampaign(Campaign $campaign, array $data): Campaign
    {
        return DB::transaction(function () use ($campaign, $data): Campaign {
            $campaign->fill(Arr::only($data, [
                'game_system_id',
                'title',
                'synopsis',
                'description',
                'rules_summary',
                'max_players',
                'visibility',
                'status',
                'language',
                'timezone',
                'frequency_label',
            ]));

            if ($campaign->isDirty('title')) {
                $campaign->slug = $this->generateUniqueSlug($campaign->title, $campaign->getKey());
            }

            $campaign->save();

            return $campaign->load(['owner', 'gameSystem'])->loadCount('members');
        });
    }

    protected function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $counter = 1;

        while (Campaign::query()
            ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $counter++;
            $slug = $base.'-'.$counter;
        }

        return $slug;
    }
}
