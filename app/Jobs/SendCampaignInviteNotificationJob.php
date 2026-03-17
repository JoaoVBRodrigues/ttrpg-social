<?php

namespace App\Jobs;

use App\Models\CampaignMember;
use App\Services\Notification\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendCampaignInviteNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $membershipId)
    {
    }

    public function handle(NotificationService $service): void
    {
        $membership = CampaignMember::query()->with(['campaign', 'user.notificationPreference'])->find($this->membershipId);

        if (! $membership) {
            return;
        }

        $service->sendCampaignInvite($membership);
    }
}
