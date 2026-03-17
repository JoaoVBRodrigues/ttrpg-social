<?php

namespace App\Listeners;

use App\Events\CampaignMemberInvited;
use App\Jobs\SendCampaignInviteNotificationJob;

class QueueCampaignInviteNotification
{
    public function handle(CampaignMemberInvited $event): void
    {
        SendCampaignInviteNotificationJob::dispatch($event->membership->getKey());
    }
}
