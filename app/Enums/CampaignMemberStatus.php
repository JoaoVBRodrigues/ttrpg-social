<?php

namespace App\Enums;

enum CampaignMemberStatus: string
{
    case INVITED = 'invited';
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case REJECTED = 'rejected';
    case REMOVED = 'removed';
    case LEFT = 'left';
}
