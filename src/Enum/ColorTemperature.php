<?php

declare(strict_types=1);

namespace App\Enum;

enum ColorTemperature: string
{
    case WARM = 'warm';
    case COOL = 'cool';
    case NEUTRAL = 'neutral';
}
