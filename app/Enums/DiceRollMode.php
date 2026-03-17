<?php

namespace App\Enums;

enum DiceRollMode: string
{
    case NORMAL = 'normal';
    case ADVANTAGE = 'advantage';
    case DISADVANTAGE = 'disadvantage';
}
