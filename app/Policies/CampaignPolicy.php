<?php

namespace App\Policies;

use App\Enums\CampaignStatus;
use App\Enums\CampaignVisibility;
use App\Models\Campaign;
use App\Models\User;

class CampaignPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Campaign $campaign): bool
    {
        if ($campaign->visibility === CampaignVisibility::PUBLIC) {
            return true;
        }

        if (! $user) {
            return false;
        }

        return $user->canManageCampaign($campaign)
            || $user->campaignMemberships()
                ->where('campaign_id', $campaign->getKey())
                ->exists();
    }

    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    public function update(User $user, Campaign $campaign): bool
    {
        return $user->canManageCampaign($campaign);
    }

    public function requestJoin(User $user, Campaign $campaign): bool
    {
        return $campaign->visibility !== CampaignVisibility::PRIVATE
            && $campaign->status === CampaignStatus::OPEN
            && ! $user->campaignMemberships()->where('campaign_id', $campaign->getKey())->exists();
    }

    public function manageMembers(User $user, Campaign $campaign): bool
    {
        return $user->canManageCampaign($campaign);
    }
}
