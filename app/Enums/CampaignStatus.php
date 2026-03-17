<?php

namespace App\Enums;

enum CampaignStatus: string
{
    case DRAFT = 'draft';
    case OPEN = 'open';
    case FULL = 'full';
    case ONGOING = 'ongoing';
    case PAUSED = 'paused';
    case FINISHED = 'finished';
    case ARCHIVED = 'archived';
}
