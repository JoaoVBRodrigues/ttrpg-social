<?php

namespace App\Enums;

enum CampaignMemberRole: string
{
    case GM = 'gm';
    case CO_GM = 'co_gm';
    case PLAYER = 'player';
    case SPECTATOR = 'spectator';
}
