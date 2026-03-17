<?php

namespace App\Listeners;

use App\Enums\CampaignSessionStatus;
use App\Events\CampaignSessionScheduled;
use App\Events\CampaignSessionUpdated;
use App\Jobs\DispatchSessionNotificationJob;
use App\Jobs\SendSessionReminderJob;

class QueueSessionNotifications
{
    public function handle(object $event): void
    {
        $kind = $event instanceof CampaignSessionScheduled ? 'scheduled' : 'updated';

        DispatchSessionNotificationJob::dispatch(
            $event->session->getKey(),
            $kind,
            $event->actor->getKey(),
        );

        if (
            $event->session->status !== CampaignSessionStatus::SCHEDULED
            || ! $event->session->starts_at?->isFuture()
        ) {
            return;
        }

        $event->session->campaign->members()
            ->where('status', 'active')
            ->get()
            ->each(function ($membership) use ($event): void {
                $job = SendSessionReminderJob::dispatch(
                    $event->session->getKey(),
                    $membership->user_id,
                );

                $reminderAt = $event->session->starts_at->copy()->subDay();

                if ($reminderAt->isFuture()) {
                    $job->delay($reminderAt);
                }
            });
    }
}
