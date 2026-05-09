<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\Outfit\Enum\PatternType;
use App\Domain\Outfit\Enum\SeasonType;
use App\Domain\Outfit\Enum\StyleType;
use App\Domain\Outfit\Enum\WeatherConditionType;
use App\Entity\Pattern;
use App\Entity\Season;
use App\Entity\Style;
use App\Entity\WeatherCondition;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LookupFixtures extends Fixture
{
    // Patterns
    public const string PATTERN_SOLID              = 'pattern-solid';
    public const string PATTERN_VERTICAL_STRIPES   = 'pattern-vertical-stripes';
    public const string PATTERN_HORIZONTAL_STRIPES = 'pattern-horizontal-stripes';
    public const string PATTERN_CHECKED            = 'pattern-checked';
    public const string PATTERN_DOTTED             = 'pattern-dotted';
    public const string PATTERN_FLORAL             = 'pattern-floral';
    public const string PATTERN_LEAF               = 'pattern-leaf';
    public const string PATTERN_NOVELTY            = 'pattern-novelty';
    public const string PATTERN_PRINT              = 'pattern-print';
    public const string PATTERN_MULTICOLOR         = 'pattern-multicolor';
    public const string PATTERN_CAMOUFLAGE         = 'pattern-camouflage';
    public const string PATTERN_PAISLEY            = 'pattern-paisley';
    public const string PATTERN_HOUNDSTOOTH        = 'pattern-houndstooth';
    public const string PATTERN_CHEVRON            = 'pattern-chevron';
    public const string PATTERN_ARGYLE             = 'pattern-argyle';
    public const string PATTERN_ZIGZAG             = 'pattern-zigzag';
    public const string PATTERN_GEOMETRIC          = 'pattern-geometric';
    public const string PATTERN_ABSTRACT           = 'pattern-abstract';
    public const string PATTERN_TIE_DYE            = 'pattern-tie-dye';
    public const string PATTERN_MARL               = 'pattern-marl';


    // Styles
    public const string STYLE_CASUAL               = 'style-casual';
    public const string STYLE_SMART_CASUAL         = 'style-smart-casual';
    public const string STYLE_BUSINESS             = 'style-business';
    public const string STYLE_BUSINESS_CASUAL      = 'style-business_casual';
    public const string STYLE_ELEGANT              = 'style-elegant';
    public const string STYLE_SPORTY               = 'style-sporty';
    public const string STYLE_STREETWEAR           = 'style-streetwear';
    public const string STYLE_VINTAGE              = 'style-vintage';
    public const string STYLE_OUTDOOR              = 'style-outdoor';
    public const string STYLE_FASHION              = 'style-fashion';
    public const string STYLE_MINIMALIST           = 'style-minimalist';
    public const string STYLE_BOHO                 = 'style-boho';
    public const string STYLE_PREPPY               = 'style-preppy';

    // Seasons
    public const string SEASON_SPRING      = 'season-spring';
    public const string SEASON_SUMMER      = 'season-summer';
    public const string SEASON_AUTUMN      = 'season-autumn';
    public const string SEASON_WINTER      = 'season-winter';

    // Weather conditions
    public const string WEATHER_SUNNY      = 'weather-sunny';
    public const string WEATHER_RAINY      = 'weather-rainy';
    public const string WEATHER_WINDY      = 'weather-windy';
    public const string WEATHER_COLD       = 'weather-cold';
    public const string WEATHER_MILD       = 'weather-mild';
    public const string WEATHER_HOT        = 'weather-hot';

    public function load(ObjectManager $manager): void
    {
        $this->loadPatterns($manager);
        $this->loadStyles($manager);
        $this->loadSeasons($manager);
        $this->loadWeatherConditions($manager);

        $manager->flush();
    }

    private function loadPatterns(ObjectManager $manager): void
    {
        $patterns = [
            self::PATTERN_SOLID              => PatternType::SOLID,
            self::PATTERN_VERTICAL_STRIPES   => PatternType::VERTICAL_STRIPES,
            self::PATTERN_HORIZONTAL_STRIPES => PatternType::HORIZONTAL_STRIPES,
            self::PATTERN_CHECKED            => PatternType::CHECKED,
            self::PATTERN_DOTTED             => PatternType::DOTTED,
            self::PATTERN_FLORAL             => PatternType::FLORAL,
            self::PATTERN_LEAF               => PatternType::LEAF,
            self::PATTERN_NOVELTY            => PatternType::NOVELTY,
            self::PATTERN_PRINT              => PatternType::PRINT,
            self::PATTERN_MULTICOLOR         => PatternType::MULTICOLOR,
            self::PATTERN_CAMOUFLAGE         => PatternType::CAMOUFLAGE,
            self::PATTERN_PAISLEY            => PatternType::PAISLEY,
            self::PATTERN_HOUNDSTOOTH        => PatternType::HOUNDSTOOTH,
            self::PATTERN_CHEVRON            => PatternType::CHEVRON,
            self::PATTERN_ARGYLE             => PatternType::ARGYLE,
            self::PATTERN_ZIGZAG             => PatternType::ZIGZAG,
            self::PATTERN_GEOMETRIC          => PatternType::GEOMETRIC,
            self::PATTERN_ABSTRACT           => PatternType::ABSTRACT,
            self::PATTERN_TIE_DYE            => PatternType::TIE_DYE,
            self::PATTERN_MARL               => PatternType::MARL,
        ];

        foreach ($patterns as $reference => $type) {
            $pattern = new Pattern();
            $pattern->setType($type);
            $manager->persist($pattern);
            $this->addReference($reference, $pattern);
        }
    }

    private function loadStyles(ObjectManager $manager): void
    {
        $styles = [
            self::STYLE_CASUAL               => StyleType::CASUAL,
            self::STYLE_SMART_CASUAL         => StyleType::SMART_CASUAL,
            self::STYLE_BUSINESS             => StyleType::BUSINESS,
            self::STYLE_BUSINESS_CASUAL      => StyleType::BUSINESS_CASUAL,
            self::STYLE_ELEGANT              => StyleType::ELEGANT,
            self::STYLE_SPORTY               => StyleType::SPORTY,
            self::STYLE_STREETWEAR           => StyleType::STREETWEAR,
            self::STYLE_VINTAGE              => StyleType::VINTAGE,
            self::STYLE_OUTDOOR              => StyleType::OUTDOOR,
            self::STYLE_FASHION              => StyleType::FASHION,
            self::STYLE_MINIMALIST           => StyleType::MINIMALIST,
            self::STYLE_BOHO                 => StyleType::BOHO,
            self::STYLE_PREPPY               => StyleType::PREPPY,
        ];

        foreach ($styles as $reference => $type) {
            $style = new Style();
            $style->setType($type);
            $manager->persist($style);
            $this->addReference($reference, $style);
        }
    }

    private function loadSeasons(ObjectManager $manager): void
    {
        $seasons = [
            self::SEASON_SPRING => SeasonType::SPRING,
            self::SEASON_SUMMER => SeasonType::SUMMER,
            self::SEASON_AUTUMN => SeasonType::AUTUMN,
            self::SEASON_WINTER => SeasonType::WINTER,
        ];

        foreach ($seasons as $reference => $type) {
            $season = new Season();
            $season->setType($type);
            $manager->persist($season);
            $this->addReference($reference, $season);
        }
    }

    private function loadWeatherConditions(ObjectManager $manager): void
    {
        $conditions = [
            self::WEATHER_SUNNY => WeatherConditionType::SUNNY,
            self::WEATHER_RAINY => WeatherConditionType::RAINY,
            self::WEATHER_WINDY => WeatherConditionType::WINDY,
            self::WEATHER_COLD  => WeatherConditionType::COLD,
            self::WEATHER_MILD  => WeatherConditionType::MILD,
            self::WEATHER_HOT   => WeatherConditionType::HOT,
        ];

        foreach ($conditions as $reference => $type) {
            $condition = new WeatherCondition();
            $condition->setType($type);
            $manager->persist($condition);
            $this->addReference($reference, $condition);
        }
    }
}
