<?php

namespace App\Providers;

use App\Events\CampaignMemberInvited;
use App\Events\CampaignMembershipReviewed;
use App\Events\CampaignSessionScheduled;
use App\Events\CampaignSessionUpdated;
use App\Listeners\QueueCampaignInviteNotification;
use App\Listeners\QueueCampaignMembershipReviewedNotification;
use App\Listeners\QueueSessionNotifications;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CampaignMemberInvited::class => [
            QueueCampaignInviteNotification::class,
        ],
        CampaignMembershipReviewed::class => [
            QueueCampaignMembershipReviewedNotification::class,
        ],
        CampaignSessionScheduled::class => [
            QueueSessionNotifications::class,
        ],
        CampaignSessionUpdated::class => [
            QueueSessionNotifications::class,
        ],
    ];
}
