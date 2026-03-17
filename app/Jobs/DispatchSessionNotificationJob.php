<?php

namespace App\Jobs;

use App\Models\CampaignSession;
use App\Services\Notification\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DispatchSessionNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $sessionId,
        public string $kind,
        public ?int $actorId = null,
    ) {
    }

    public function handle(NotificationService $service): void
    {
        $session = CampaignSession::query()->with(['campaign.members.user.notificationPreference'])->find($this->sessionId);

        if (! $session) {
            return;
        }

        if ($this->kind === 'scheduled') {
            $service->sendSessionScheduled($session, $this->actorId);
        }

        if ($this->kind === 'updated') {
            $service->sendSessionUpdated($session, $this->actorId);
        }
    }
}
