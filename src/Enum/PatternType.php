<?php

declare(strict_types=1);

namespace App\Enum;

enum PatternType: string
{
    case SOLID = 'solid';
    case VERTICAL_STRIPES = 'vertical_stripes';
    case HORIZONTAL_STRIPES = 'horizontal_stripes';
    case CHECKED = 'checked';
    case DOTTED = 'dotted';
    case FLORAL = 'floral';
    case LEAF = 'leaf';
    case PRINT = 'print';
    case NOVELTY = 'novelty';
    case MULTICOLOR = 'multicolor';
}
