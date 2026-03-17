<?php

namespace App\Listeners;

use App\Events\CampaignMembershipReviewed;
use App\Jobs\SendCampaignMembershipReviewedNotificationJob;

class QueueCampaignMembershipReviewedNotification
{
    public function handle(CampaignMembershipReviewed $event): void
    {
        SendCampaignMembershipReviewedNotificationJob::dispatch($event->membership->getKey());
    }
}
