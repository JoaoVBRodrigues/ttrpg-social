<?php

use App\Enums\CampaignMemberStatus;
use App\Models\CampaignMember;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('campaign.{campaignId}', function (User $user, int $campaignId): bool {
    return CampaignMember::query()
        ->where('campaign_id', $campaignId)
        ->where('user_id', $user->getKey())
        ->where('status', CampaignMemberStatus::ACTIVE->value)
        ->exists();
});
