<?php

declare(strict_types=1);

namespace App\Domain\Outfit\Enum;

enum StyleType: string
{
    case BUSINESS_CASUAL = 'business_casual';
    case BUSINESS        = 'business';
    case SMART_CASUAL    = 'smart_casual';
    case CASUAL          = 'casual';
    case ELEGANT         = 'elegant';
    case SPORTY          = 'sporty';
    case STREETWEAR      = 'streetwear';
    case VINTAGE         = 'vintage';
    case OUTDOOR         = 'outdoor';
    case FASHION         = 'fashion';
    case MINIMALIST      = 'minimalist';
    case BOHO            = 'boho';
    case PREPPY          = 'preppy';
}
