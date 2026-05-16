<?php

declare(strict_types=1);

namespace App\Tests\Unit\Outfit\Rule;

use App\ClothingItem\Enum\SeasonType;
use App\ClothingItem\Enum\WeatherConditionType;
use App\Entity\ClothingItem;
use App\Entity\Season;
use App\Entity\WeatherCondition;
use App\Outfit\Rule\SeasonCompatibilityService;
use PHPUnit\Framework\TestCase;

class SeasonCompatibilityServiceTest extends TestCase
{
    private SeasonCompatibilityService $service;

    protected function setUp(): void
    {
        $this->service = new SeasonCompatibilityService();
    }

    // --- Helper method ---

    private function makeSeason(SeasonType $type): Season
    {
        $season = new Season();
        $season->setType($type);
        return $season;
    }

    private function makeWeather(WeatherConditionType $type): WeatherCondition
    {
        $weather = new WeatherCondition();
        $weather->setType($type);
        return $weather;
    }

    /**
     * @param array<int, Season> $seasons
     * @param array<int, WeatherCondition> $weatherConditions
     * @return ClothingItem
     */
    private function makeItem(array $seasons = [], array $weatherConditions = []): ClothingItem
    {
        $item = new ClothingItem();

        foreach ($seasons as $season) {
            $item->addSeason($season);
        }

        foreach ($weatherConditions as $weather) {
            $item->addWeatherCondition($weather);
        }

        return $item;
    }

    // -------------------------------------------------------
    // Saison-Filter: no entry = for all seasons
    // -------------------------------------------------------

    public function testItemWithNoSeasonsIsCompatibleWithAnySeason(): void
    {
        $item = $this->makeItem();

        $this->assertTrue(
            $this->service->isCompatible($item, SeasonType::WINTER)
        );
    }

    public function testItemWithMatchingSeasonIsCompatible(): void
    {
        $item = $this->makeItem([
            $this->makeSeason(SeasonType::AUTUMN),
            $this->makeSeason(SeasonType::SPRING),
        ]);

        $this->assertTrue(
            $this->service->isCompatible($item, SeasonType::AUTUMN)
        );
    }

    public function testItemWithNonMatchingSeasonIsNotCompatible(): void
    {
        $item = $this->makeItem([
            $this->makeSeason(SeasonType::SUMMER),
        ]);

        $this->assertFalse(
            $this->service->isCompatible($item, SeasonType::WINTER)
        );
    }

    public function testTrenchcoatIsCompatibleWithAutumnAndSpring(): void
    {
        $trenchcoat = $this->makeItem([
            $this->makeSeason(SeasonType::AUTUMN),
            $this->makeSeason(SeasonType::SPRING),
        ]);

        $this->assertTrue($this->service->isCompatible($trenchcoat, SeasonType::AUTUMN));
        $this->assertTrue($this->service->isCompatible($trenchcoat, SeasonType::SPRING));
        $this->assertFalse($this->service->isCompatible($trenchcoat, SeasonType::SUMMER));
        $this->assertFalse($this->service->isCompatible($trenchcoat, SeasonType::WINTER));
    }

    // -------------------------------------------------------
    // Weather-Filter: no entry = for all weather
    // -------------------------------------------------------

    public function testItemWithNoWeatherIsCompatibleWithAnyWeather(): void
    {
        $item = $this->makeItem(
            [$this->makeSeason(SeasonType::SUMMER)],
            [] // no Weather set
        );

        $this->assertTrue(
            $this->service->isCompatible($item, SeasonType::SUMMER, WeatherConditionType::SUNNY)
        );
    }

    public function testItemWithMatchingWeatherIsCompatible(): void
    {
        $item = $this->makeItem(
            [$this->makeSeason(SeasonType::AUTUMN)],
            [$this->makeWeather(WeatherConditionType::RAINY)]
        );

        $this->assertTrue(
            $this->service->isCompatible($item, SeasonType::AUTUMN, WeatherConditionType::RAINY)
        );
    }

    public function testItemWithNonMatchingWeatherIsNotCompatible(): void
    {
        $item = $this->makeItem(
            [$this->makeSeason(SeasonType::SUMMER)],
            [$this->makeWeather(WeatherConditionType::SUNNY)]
        );

        $this->assertFalse(
            $this->service->isCompatible($item, SeasonType::SUMMER, WeatherConditionType::RAINY)
        );
    }

    // -------------------------------------------------------
    // Combination of Season and WeatherCondition
    // -------------------------------------------------------

    public function testRaincoatIsCompatibleInRainyAutumn(): void
    {
        $raincoat = $this->makeItem(
            [
                $this->makeSeason(SeasonType::AUTUMN),
                $this->makeSeason(SeasonType::SPRING),
            ],
            [
                $this->makeWeather(WeatherConditionType::RAINY),
                $this->makeWeather(WeatherConditionType::WINDY),
            ]
        );

        $this->assertTrue(
            $this->service->isCompatible($raincoat, SeasonType::AUTUMN, WeatherConditionType::RAINY)
        );
    }

    public function testRaincoatIsNotCompatibleInSunnySummer(): void
    {
        $raincoat = $this->makeItem(
            [
                $this->makeSeason(SeasonType::AUTUMN),
                $this->makeSeason(SeasonType::SPRING),
            ],
            [
                $this->makeWeather(WeatherConditionType::RAINY),
            ]
        );

        $this->assertFalse(
            $this->service->isCompatible($raincoat, SeasonType::SUMMER, WeatherConditionType::SUNNY)
        );
    }

    public function testWhiteShirtWithNoConditionsIsAlwaysCompatible(): void
    {
        // White Shirt - no seasons, no weather conditions = universal
        $shirt = $this->makeItem();

        $this->assertTrue($this->service->isCompatible($shirt, SeasonType::WINTER, WeatherConditionType::COLD));
        $this->assertTrue($this->service->isCompatible($shirt, SeasonType::SUMMER, WeatherConditionType::HOT));
        $this->assertTrue($this->service->isCompatible($shirt, SeasonType::AUTUMN, WeatherConditionType::RAINY));
    }

    // -------------------------------------------------------
    // Without Weather-Argument: check only season
    // -------------------------------------------------------

    public function testCompatibilityWithoutWeatherArgumentChecksSeasonOnly(): void
    {
        $item = $this->makeItem(
            [$this->makeSeason(SeasonType::WINTER)],
            [$this->makeWeather(WeatherConditionType::COLD)]
        );

        // no weather transferred → season is checked
        $this->assertTrue(
            $this->service->isCompatible($item, SeasonType::WINTER)
        );
        $this->assertFalse(
            $this->service->isCompatible($item, SeasonType::SUMMER)
        );
    }
}
