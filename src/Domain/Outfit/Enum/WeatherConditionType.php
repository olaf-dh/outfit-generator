<?php

declare(strict_types=1);

namespace App\Domain\Outfit\Enum;

enum WeatherConditionType: string
{
    case SUNNY = 'sunny';
    case COLD = 'cold';
    case RAINY = 'rainy';
    case WINDY = 'windy';
    case MILD = 'mild';
    case HOT = 'hot';
}
