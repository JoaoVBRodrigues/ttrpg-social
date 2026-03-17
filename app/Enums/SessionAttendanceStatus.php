<?php

namespace App\Enums;

enum SessionAttendanceStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case MAYBE = 'maybe';
    case DECLINED = 'declined';
}
