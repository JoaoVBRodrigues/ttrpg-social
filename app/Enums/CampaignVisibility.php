<?php

namespace App\Enums;

enum CampaignVisibility: string
{
    case PUBLIC = 'public';
    case UNLISTED = 'unlisted';
    case PRIVATE = 'private';
}
