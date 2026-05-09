<?php

declare(strict_types=1);

namespace App\Domain\Outfit\Enum;

enum BreathabilityLevel: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
}
