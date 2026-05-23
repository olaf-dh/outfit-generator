<?php

declare(strict_types=1);

namespace App\Color\Enum;

enum ColorSaturation: string
{
    case MUTED = 'muted';
    case SOFT = 'soft';
    case MEDIUM = 'medium';
    case VIBRANT = 'vibrant';
}
