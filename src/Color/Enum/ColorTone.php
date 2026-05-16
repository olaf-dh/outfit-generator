<?php

declare(strict_types=1);

namespace App\Color\Enum;

enum ColorTone: string
{
    case LIGHT = 'light';
    case DARK = 'dark';
    case MEDIUM = 'medium';
}
