<?php

declare(strict_types=1);

namespace App\Enum;

enum StyleType: string
{
    case BUSINESS = 'business';
    case SMART_CASUAL = 'smart_casual';
    case CASUAL = 'casual';
    case FORMAL = 'formal';
    case SPORTY = 'sporty';
}
