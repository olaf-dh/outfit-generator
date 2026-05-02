<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Color;
use App\Entity\Material;
use App\Entity\Pattern;
use App\Entity\Season;
use App\Entity\Style;
use App\Entity\WeatherCondition;
use App\Enum\ColorFamily;
use App\Enum\ColorSaturation;
use App\Enum\ColorTemperature;
use App\Enum\ColorTone;
use App\Enum\PatternType;
use App\Enum\SeasonType;
use App\Enum\StyleType;
use App\Enum\WeatherConditionType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LookupFixtures extends Fixture
{
    // Colors
    public const string COLOR_WHITE        = 'color-white';
    public const string COLOR_BLACK        = 'color-black';
    public const string COLOR_LIGHT_GRAY   = 'color-light-gray';
    public const string COLOR_MEDIUM_GRAY  = 'color-medium-gray';
    public const string COLOR_ANTHRACITE   = 'color-anthracite';
    public const string COLOR_NAVY         = 'color-navy';
    public const string COLOR_BEIGE        = 'color-beige';
    public const string COLOR_COGNAC       = 'color-cognac';
    public const string COLOR_BORDEAUX     = 'color-bordeaux';
    public const string COLOR_RED_VIVID    = 'color-red-vivid';
    public const string COLOR_OLIVE        = 'color-olive';
    public const string COLOR_DARK_BLUE    = 'color-dark-blue';
    public const string COLOR_CAMEL        = 'color-camel';

    // Materials
    public const string MATERIAL_COTTON    = 'material-cotton';
    public const string MATERIAL_WOOL      = 'material-wool';
    public const string MATERIAL_CASHMERE  = 'material-cashmere';
    public const string MATERIAL_LEATHER   = 'material-leather';
    public const string MATERIAL_LINEN     = 'material-linen';
    public const string MATERIAL_DENIM     = 'material-denim';
    public const string MATERIAL_POLYESTER = 'material-polyester';

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

    // Styles
    public const string STYLE_CASUAL       = 'style-casual';
    public const string STYLE_SMART_CASUAL = 'style-smart-casual';
    public const string STYLE_BUSINESS     = 'style-business';

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
        $this->loadColors($manager);
        $this->loadMaterials($manager);
        $this->loadPatterns($manager);
        $this->loadStyles($manager);
        $this->loadSeasons($manager);
        $this->loadWeatherConditions($manager);

        $manager->flush();
    }

    private function loadColors(ObjectManager $manager): void
    {
        $colors = [
            self::COLOR_WHITE => [
                'name'   => 'Weiß',
                'hex'    => '#FFFFFF',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_BLACK => [
                'name'   => 'Schwarz',
                'hex'    => '#0A0A0A',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_LIGHT_GRAY => [
                'name'   => 'Hellgrau',
                'hex'    => '#C8C8C8',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_MEDIUM_GRAY => [
                'name'   => 'Mittelgrau',
                'hex'    => '#808080',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_ANTHRACITE => [
                'name'   => 'Anthrazit',
                'hex'    => '#2F2F2F',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_NAVY => [
                'name'   => 'Navy',
                'hex'    => '#1B2A4A',
                'family' => ColorFamily::NAVY,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_BEIGE => [
                'name'   => 'Beige',
                'hex'    => '#F5F0E8',
                'family' => ColorFamily::BEIGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_COGNAC => [
                'name'   => 'Cognac',
                'hex'    => '#9A4F2D',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_BORDEAUX => [
                'name'   => 'Bordeaux',
                'hex'    => '#5C1F2E',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_RED_VIVID => [
                'name'   => 'Rot',
                'hex'    => '#CC2200',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIVID
            ],
            self::COLOR_OLIVE => [
                'name'   => 'Oliv',
                'hex'    => '#6B6B2A',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_DARK_BLUE => [
                'name'   => 'Dunkelblau',
                'hex'    => '#1A2F5A',
                'family' => ColorFamily::NAVY,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_CAMEL => [
                'name'   => 'Camel',
                'hex'    => '#C19A6B',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL
            ],
        ];

        foreach ($colors as $reference => $data) {
            $color = new Color();
            $color->setName($data['name']);
            $color->setHexCode($data['hex']);
            $color->setFamily($data['family']);
            $color->setTone($data['tone']);
            $color->setTemperature($data['temp']);
            $color->setSaturation($data['sat']);
            $manager->persist($color);
            $this->addReference($reference, $color);
        }
    }

    private function loadMaterials(ObjectManager $manager): void
    {
        $materials = [
            self::MATERIAL_COTTON    => 'Baumwolle',
            self::MATERIAL_WOOL      => 'Wolle',
            self::MATERIAL_CASHMERE  => 'Kaschmir',
            self::MATERIAL_LEATHER   => 'Leder',
            self::MATERIAL_LINEN     => 'Leinen',
            self::MATERIAL_DENIM     => 'Denim',
            self::MATERIAL_POLYESTER => 'Polyester',
        ];

        foreach ($materials as $reference => $name) {
            $material = new Material();
            $material->setName($name);
            $manager->persist($material);
            $this->addReference($reference, $material);
        }
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
            self::STYLE_CASUAL       => StyleType::CASUAL,
            self::STYLE_SMART_CASUAL => StyleType::SMART_CASUAL,
            self::STYLE_BUSINESS     => StyleType::BUSINESS,
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
