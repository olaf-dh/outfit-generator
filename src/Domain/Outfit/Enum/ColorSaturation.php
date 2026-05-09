<?php

declare(strict_types=1);

namespace App\Domain\Outfit\Enum;

enum ColorSaturation: string
{
    case MUTED = 'muted';
    case NORMAL = 'normal';
    case VIVID = 'vivid';
}
