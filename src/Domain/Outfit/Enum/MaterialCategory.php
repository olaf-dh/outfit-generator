<?php

declare(strict_types=1);

namespace App\Domain\Outfit\Enum;

enum MaterialCategory: string
{
    case NATURAL = 'natural';
    case SYNTHETIC = 'synthetic';
    case TEXTURE = 'texture';
    case FUNCTIONAL = 'functional';
}
