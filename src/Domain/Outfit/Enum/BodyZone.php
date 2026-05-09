<?php

declare(strict_types=1);

namespace App\Domain\Outfit\Enum;

enum BodyZone: string
{
    case UPPER_BODY = 'upper_body';
    case LOWER_BODY = 'lower_body';
    case FULL_BODY = 'full_body';
    case FOOTWEAR = 'footwear';
    case OUTER_LAYER = 'outer_layer';
    case HEAD = 'head';
    case ACCESSORY = 'accessory';
}
