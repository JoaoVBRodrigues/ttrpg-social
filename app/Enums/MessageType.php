<?php

namespace App\Enums;

enum MessageType: string
{
    case TEXT = 'text';
    case SYSTEM = 'system';
    case DICE_ROLL = 'dice_roll';
    case SESSION_NOTICE = 'session_notice';
}
