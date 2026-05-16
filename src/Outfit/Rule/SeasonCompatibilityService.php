<?php

declare(strict_types=1);

namespace App\Outfit\Rule;

use App\ClothingItem\Enum\SeasonType;
use App\ClothingItem\Enum\WeatherConditionType;
use App\Entity\ClothingItem;
use App\Entity\Season;
use App\Entity\WeatherCondition;

class SeasonCompatibilityService
{
    public function isCompatible(ClothingItem $item, SeasonType $season, ?WeatherConditionType $weather = null): bool
    {
        // Rule 1: If the item has no season, it's compatible with any season
        if (!$item->getSeasons()->isEmpty()) {
            // Rule 2: If the item has a season, it's compatible with that season
            $seasonMatches = $item->getSeasons()->exists(
                fn(int $key, Season $s) => $s->getType() === $season
            );

            if (!$seasonMatches) {
                return false;
            }
        }

        // Rule 3: No weather parameter set = only season is relevant
        if ($weather === null) {
            return true;
        }

        // Rule 4: No weather condition set = compatible with any weather condition
        if ($item->getWeatherConditions()->isEmpty()) {
            return true;
        }

        // Rule 5: Weather condition set = compatible with that weather condition
        return $item->getWeatherConditions()->exists(
            fn(int $key, WeatherCondition $w) => $w->getType() === $weather
        );
    }
}
