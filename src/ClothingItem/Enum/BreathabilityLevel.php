<?php

declare(strict_types=1);

namespace App\ClothingItem\Enum;

enum BreathabilityLevel: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
}
