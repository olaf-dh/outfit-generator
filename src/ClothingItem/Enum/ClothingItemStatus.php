<?php

declare(strict_types=1);

namespace App\ClothingItem\Enum;

enum ClothingItemStatus: string
{
    case PENDING = 'pending';
    case ANALYZED = 'analyzed';
    case COMPLETE = 'complete';
}
