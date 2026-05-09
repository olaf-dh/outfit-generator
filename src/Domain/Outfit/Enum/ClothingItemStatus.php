<?php

declare(strict_types=1);

namespace App\Domain\Outfit\Enum;

enum ClothingItemStatus: string
{
    case PENDING = 'pending';
    case ANALYZED = 'analyzed';
    case COMPLETE = 'complete';
}
