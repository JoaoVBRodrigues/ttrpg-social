<?php

namespace App\Jobs;

use App\Models\Message;
use App\Services\Notification\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DispatchImportantMessageNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $messageId)
    {
    }

    public function handle(NotificationService $service): void
    {
        $message = Message::query()->with(['campaign.members.user.notificationPreference', 'user'])->find($this->messageId);

        if (! $message) {
            return;
        }

        $service->sendImportantMessage($message);
    }
}
