<?php

namespace App\Events;

use App\Models\CampaignSession;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampaignSessionScheduled
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public CampaignSession $session,
        public User $actor,
    ) {
    }
}
