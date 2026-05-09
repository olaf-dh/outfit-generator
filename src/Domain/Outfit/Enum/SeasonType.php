<?php

declare(strict_types=1);

namespace App\Domain\Outfit\Enum;

enum SeasonType: string
{
    case SPRING = 'spring';
    case SUMMER = 'summer';
    case AUTUMN = 'autumn';
    case WINTER = 'winter';
}
