<?php

namespace App\Listeners;

use App\Events\ImportantCampaignMessageCreated;
use App\Jobs\DispatchImportantMessageNotificationJob;

class QueueImportantMessageNotifications
{
    public function handle(ImportantCampaignMessageCreated $event): void
    {
        DispatchImportantMessageNotificationJob::dispatch($event->message->getKey());
    }
}
