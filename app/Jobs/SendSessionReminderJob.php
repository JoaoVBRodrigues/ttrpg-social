<?php

namespace App\Jobs;

use App\Models\CampaignSession;
use App\Models\User;
use App\Services\Notification\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendSessionReminderJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $sessionId,
        public int $userId,
    ) {
    }

    public function handle(NotificationService $service): void
    {
        $session = CampaignSession::query()->with('campaign')->find($this->sessionId);
        $user = User::query()->find($this->userId);

        if (! $session || ! $user) {
            return;
        }

        $service->sendSessionReminder($session, $user);
    }
}
