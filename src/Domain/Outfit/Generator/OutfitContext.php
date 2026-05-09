<?php

declare(strict_types=1);

namespace App\Domain\Outfit\Generator;

use App\Domain\Outfit\Enum\SeasonType;
use App\Domain\Outfit\Enum\WeatherConditionType;

final class OutfitContext
{
    /**
     * @param int $temperature
     * @param SeasonType|null $season
     * @param WeatherConditionType[] $weatherConditions
     * @param bool $isRaining
     * @param bool $isWindy
     */
    public function __construct(
        public int $temperature = 20,
        public ?SeasonType $season = null,
        public array $weatherConditions = [],
        public bool $isRaining = false,
        public bool $isWindy = false,
    ) {
    }

    public function getTemperature(): int
    {
        return $this->temperature;
    }

    public function getSeason(): ?SeasonType
    {
        return $this->season;
    }

    /**
     * @return WeatherConditionType[]
     */
    public function getWeatherConditions(): array
    {
        return $this->weatherConditions;
    }

    public function isRaining(): bool
    {
        return $this->isRaining;
    }

    public function isWindy(): bool
    {
        return $this->isWindy;
    }

    public function hasWeatherCondition(WeatherConditionType $condition): bool
    {
        return in_array($condition, $this->weatherConditions, true);
    }
}
