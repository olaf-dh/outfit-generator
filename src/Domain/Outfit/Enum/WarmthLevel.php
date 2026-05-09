<?php

declare(strict_types=1);

namespace App\Domain\Outfit\Enum;

enum WarmthLevel: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
}
