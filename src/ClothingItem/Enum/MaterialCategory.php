<?php

declare(strict_types=1);

namespace App\ClothingItem\Enum;

enum MaterialCategory: string
{
    case NATURAL = 'natural';
    case SYNTHETIC = 'synthetic';
    case TEXTURE = 'texture';
    case FUNCTIONAL = 'functional';
}
