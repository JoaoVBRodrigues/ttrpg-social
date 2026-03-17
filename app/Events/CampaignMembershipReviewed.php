<?php

namespace App\Events;

use App\Models\CampaignMember;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampaignMembershipReviewed
{
    use Dispatchable, SerializesModels;

    public function __construct(public CampaignMember $membership)
    {
    }
}
