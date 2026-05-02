<?php

declare(strict_types=1);

namespace App\Enum;

enum ColorSaturation: string
{
    case MUTED = 'muted';
    case NORMAL = 'normal';
    case VIVID = 'vivid';
}
