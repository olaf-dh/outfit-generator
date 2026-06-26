<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Color\Enum\ColorFamily;
use App\Color\Enum\ColorSaturation;
use App\Color\Enum\ColorTemperature;
use App\Color\Enum\ColorTone;
use App\Color\Service\ColorConverterService;
use App\Entity\Color;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class ColorFixtures extends Fixture implements FixtureGroupInterface
{
    // Colors
    // black family
    public const string COLOR_PURE_BLACK      = 'color-pure-black';
    public const string COLOR_SOFT_BLACK     = 'color-soft-black';
    public const string COLOR_JET_BLACK      = 'color-jet-black';
    public const string COLOR_ALMOST_BLACK   = 'color-almost-black';
    public const string COLOR_CHARCOAL       = 'color-charcoal';
    public const string COLOR_GRAY_BLACK     = 'color-gray-black';
    public const string COLOR_NIGHT_BLACK    = 'color-night-black';
    public const string COLOR_DEEP_BLACK     = 'color-deep-black';

    // white family
    public const string COLOR_WHITE          = 'color-white';
    public const string COLOR_SNOW_WHITE     = 'color-snow-white';
    public const string COLOR_GAINSBORO      = 'color-gainsboro';
    public const string COLOR_OFF_WHITE      = 'color-off-white';
    public const string COLOR_GHOST_WHITE    = 'color-ghost-white';
    public const string COLOR_ALMOST_WHITE   = 'color-almost-white';
    public const string COLOR_GRAY_WHITE     = 'color-gray-white';
    public const string COLOR_LIGHTEST_GRAY  = 'color-lightest-gray';

    // gray family
    public const string COLOR_PURE_GRAY      = 'color-pure-gray';
    public const string COLOR_LIGHT_GRAY     = 'color-light-gray';
    public const string COLOR_DARK_GRAY      = 'color-dark-gray';
    public const string COLOR_MEDIUM_GRAY    = 'color-medium-gray';
    public const string COLOR_BRIGHT_GRAY    = 'color-bright-gray';
    public const string COLOR_SILVER         = 'color-silver';
    public const string COLOR_LIGHT_SILVER   = 'color-light-silver';
    public const string COLOR_MIST           = 'color-mist';

    // beige family
    public const string COLOR_BEIGE          = 'color-beige';
    public const string COLOR_LIGHT_BEIGE    = 'color-light-beige';
    public const string COLOR_PALE_BEIGE     = 'color-pale-beige';
    public const string COLOR_SAND_BEIGE     = 'color-sand-beige';
    public const string COLOR_SOFT_SAND      = 'color-soft-sand';
    public const string COLOR_DARK_BEIGE     = 'color-dark-beige';

    // brown family
    public const string COLOR_PERU           = 'color-peru';
    public const string COLOR_CHOCOLATE      = 'color-chocolate';
    public const string COLOR_SIENNA         = 'color-sienna';
    public const string COLOR_BROWN_RED      = 'color-brown-red';
    public const string COLOR_SADDLE_BROWN   = 'color-saddle-brown';
    public const string COLOR_MAROON         = 'color-maroon';
    public const string COLOR_DARK_BROWN     = 'color-dark-brown';

    // pink family
    public const string COLOR_PINK           = 'color-pink';
    public const string COLOR_HOT_PINK       = 'color-hot-pink';
    public const string COLOR_SALMON_PINK    = 'color-salmon-pink';
    public const string COLOR_PALE_VIOLET_RED = 'color-pale-violet-red';
    public const string COLOR_DEEP_PINK      = 'color-deep-pink';
    public const string COLOR_PASTEL_PINK    = 'color-pastel-pink';

    // red family
    public const string COLOR_PURE_RED       = 'color-pure-red';
    public const string COLOR_DARK_RED       = 'color-dark-red';
    public const string COLOR_INDIAN_RED     = 'color-indian-red';
    public const string COLOR_CRIMSON        = 'color-crimson';
    public const string COLOR_LIGHT_CORAL    = 'color-light-coral';
    public const string COLOR_BRICK_RED      = 'color-brick-red';
    public const string COLOR_TOMATO         = 'color-tomato';

    // orange family
    public const string COLOR_CORAL          = 'color-coral';
    public const string COLOR_LIGHT_ORANGE   = 'color-light-orange';
    public const string COLOR_ORANGE_GOLD    = 'color-orange-gold';
    public const string COLOR_ORANGE_PEEL    = 'color-orange-peel';
    public const string COLOR_PEACH          = 'color-peach';
    public const string COLOR_DARK_ORANGE    = 'color-dark-orange';
    public const string COLOR_MANGO_ORANGE   = 'color-mango-orange';
    public const string COLOR_BRIGHT_ORANGE  = 'color-bright-orange';

    // yellow family
    public const string COLOR_PURE_YELLOW    = 'color-pure-yellow';
//    public const string COLOR_PEACH_PUFF     = 'color-peach-puff';
    public const string COLOR_GOLD           = 'color-gold';
    public const string COLOR_LIGHT_GOLDEN   = 'color-light-golden';
    public const string COLOR_MOCCASIN       = 'color-moccasin';
    public const string COLOR_GOLDENROD      = 'color-goldenrod';
    public const string COLOR_DARK_GOLDENROD = 'color-dark-goldenrod';
    public const string COLOR_BRIGHT_GOLD    = 'color-bright-gold';

    // green family`
    public const string COLOR_LIGHT_GREEN    = 'color-light-green';
    public const string COLOR_PURE_GREEN     = 'color-pure-green';
    public const string COLOR_LIME           = 'color-lime';
    public const string COLOR_SEA_GREEN      = 'color-sea-green';
    public const string COLOR_OLIVE_DRAB     = 'color-olive-drab';
    public const string COLOR_DARK_OLIVE     = 'color-dark-olive';
    public const string COLOR_OFFICE_GREEN   = 'color-office-green';
    public const string COLOR_FOREST         = 'color-forest';

    // blue family
    public const string COLOR_NAVY           = 'color-navy';
    public const string COLOR_DEEP_SKY_BLUE  = 'color-deep-sky-blue';
    public const string COLOR_CORNFLOWER     = 'color-cornflower';
    public const string COLOR_ROYAL_BLUE     = 'color-royal-blue';
    public const string COLOR_PURE_BLUE     = 'color-pure-blue';
    public const string COLOR_DODGER_BLUE    = 'color-dodger-blue';
    public const string COLOR_LIGHT_BLUE     = 'color-light-blue';
    public const string COLOR_SKY_BLUE       = 'color-sky-blue';

    // purple family
    public const string COLOR_DARK_ORCHID    = 'color-dark-orchid';
    public const string COLOR_REBECCA_PURPLE = 'color-rebecca-purple';
    public const string COLOR_MEDIUM_PURPLE  = 'color-medium-purple';
    public const string COLOR_DARK_VIOLET    = 'color-dark-violet';
    public const string COLOR_PURE_PURPLE    = 'color-pure-purple';
    public const string COLOR_THISTLE        = 'color-thistle';
    public const string COLOR_PLUM           = 'color-plum';
    public const string COLOR_MEDIUM_ORCHID  = 'color-medium-orchid';

    public function __construct(private readonly ColorConverterService $converter)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $colors = [
            // ⚫ BLACK
            self::COLOR_PURE_BLACK => [
                'name'   => 'pure_black',
                'hex'    => '#000000',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_ALMOST_BLACK => [
                'name'   => 'almost_black',
                'hex'    => '#050505',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_DEEP_BLACK => [
                'name'   => 'deep_black',
                'hex'    => '#0A0A0A',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_JET_BLACK => [
                'name'   => 'jet_black',
                'hex'    => '#101010',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_CHARCOAL => [
                'name'   => 'charcoal',
                'hex'    => '#151515',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_GRAY_BLACK => [
                'name'   => 'gray_black',
                'hex'    => '#1A1A1A',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_NIGHT_BLACK => [
                'name'   => 'night_black',
                'hex'    => '#202020',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_SOFT_BLACK => [
                'name'   => 'soft_black',
                'hex'    => '#252525',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],

            // ⚪ WHITE
            self::COLOR_WHITE => [
                'name'   => 'white',
                'hex'    => '#FFFFFF',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_ALMOST_WHITE => [
                'name'   => 'almost_white',
                'hex'    => '#FDFDFD',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_GHOST_WHITE => [
                'name'   => 'ghost_white',
                'hex'    => '#FAFAFA',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_OFF_WHITE => [
                'name'   => 'off_white',
                'hex'    => '#F8F8F8',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_SNOW_WHITE => [
                'name'   => 'snow_white',
                'hex'    => '#F5F5F5',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_GRAY_WHITE => [
                'name'   => 'gray_white',
                'hex'    => '#F0F0F0',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_LIGHTEST_GRAY => [
                'name'   => 'lightest_gray',
                'hex'    => '#E8E8E8',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_GAINSBORO => [
                'name'   => 'gainsboro',
                'hex'    => '#DCDCDC',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],

            // ⚫ GRAY
            self::COLOR_PURE_GRAY => [
                'name'   => 'pure_gray',
                'hex'    => '#808080',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_DARK_GRAY => [
                'name'   => 'dark_gray',
                'hex'    => '#8A8A8A',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_SILVER => [
                'name'   => 'silver',
                'hex'    => '#909090',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_MEDIUM_GRAY => [
                'name'   => 'medium_gray',
                'hex'    => '#9A9A9A',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_LIGHT_GRAY => [
                'name'   => 'light_gray',
                'hex'    => '#A0A0A0',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_BRIGHT_GRAY => [
                'name'   => 'bright_gray',
                'hex'    => '#A8A8A8',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_LIGHT_SILVER => [
                'name'   => 'light_silver',
                'hex'    => '#B0B0B0',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_MIST => [
                'name'   => 'mist',
                'hex'    => '#B8B8B8',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],

            // 🟫 BEIGE
            self::COLOR_BEIGE => [
                'name'   => 'beige',
                'hex'    => '#F5F5DC',
                'family' => ColorFamily::BEIGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_LIGHT_BEIGE => [
                'name'   => 'light_beige',
                'hex'    => '#E6E6D6',
                'family' => ColorFamily::BEIGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_PALE_BEIGE => [
                'name'   => 'pale_beige',
                'hex'    => '#D3D3C7',
                'family' => ColorFamily::BEIGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_SAND_BEIGE => [
                'name'   => 'sand_beige',
                'hex'    => '#C7C7B7',
                'family' => ColorFamily::BEIGE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_SOFT_SAND => [
                'name'   => 'soft_sand',
                'hex'    => '#B7B7A7',
                'family' => ColorFamily::BEIGE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_DARK_BEIGE => [
                'name'   => 'dark_beige',
                'hex'    => '#ADAD97',
                'family' => ColorFamily::BEIGE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],

            // 🟤 BROWN
            self::COLOR_PERU => [
                'name'   => 'peru',
                'hex'    => '#CD853F',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_CHOCOLATE => [
                'name'   => 'chocolate',
                'hex'    => '#D2691E',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_SIENNA => [
                'name'   => 'sienna',
                'hex'    => '#A0522D',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_BROWN_RED => [
                'name'   => 'brown_red',
                'hex'    => '#A52A2A',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_SADDLE_BROWN => [
                'name'   => 'saddle_brown',
                'hex'    => '#8B4513',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_DARK_BROWN => [
                'name'   => 'dark_brown',
                'hex'    => '#654321',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_MAROON => [
                'name'   => 'maroon',
                'hex'    => '#800000',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],

            // 🩷 PINK
            self::COLOR_PASTEL_PINK => [
                'name'   => 'pastel_pink',
                'hex'    => '#FFD1DC',
                'family' => ColorFamily::PINK,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_PINK => [
                'name'   => 'pink',
                'hex'    => '#FFB6C1',
                'family' => ColorFamily::PINK,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_SALMON_PINK => [
                'name'   => 'salmon_pink',
                'hex'    => '#FF91A4',
                'family' => ColorFamily::PINK,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_HOT_PINK => [
                'name'   => 'hot_pink',
                'hex'    => '#FF69B4',
                'family' => ColorFamily::PINK,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_PALE_VIOLET_RED => [
                'name'   => 'pale_violet_red',
                'hex'    => '#DB7093',
                'family' => ColorFamily::PINK,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_DEEP_PINK => [
                'name'   => 'deep_pink',
                'hex'    => '#FF1493',
                'family' => ColorFamily::PINK,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],

            // 🔴 RED
            self::COLOR_LIGHT_CORAL => [
                'name'   => 'light_coral',
                'hex'    => '#F08080',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_TOMATO => [
                'name'   => 'tomato',
                'hex'    => '#FF6347',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_INDIAN_RED => [
                'name'   => 'indian_red',
                'hex'    => '#CD5C5C',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_PURE_RED => [
                'name'   => 'pure_red',
                'hex'    => '#FF0000',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_CRIMSON => [
                'name'   => 'crimson',
                'hex'    => '#DC143C',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_BRICK_RED => [
                'name'   => 'brick_red',
                'hex'    => '#B22222',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_DARK_RED => [
                'name'   => 'dark_red',
                'hex'    => '#8B0000',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],

            // 🟠 ORANGE
            self::COLOR_PEACH => [
                'name'   => 'peach',
                'hex'    => '#FFCC99',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_LIGHT_ORANGE => [
                'name'   => 'light_orange',
                'hex'    => '#FFB347',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_ORANGE_PEEL => [
                'name'   => 'orange_peel',
                'hex'    => '#FFA500',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_ORANGE_GOLD => [
                'name'   => 'orange_gold',
                'hex'    => '#FF9933',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_DARK_ORANGE => [
                'name'   => 'dark_orange',
                'hex'    => '#FF8C00',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_CORAL => [
                'name'   => 'coral',
                'hex'    => '#FF7F50',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_MANGO_ORANGE => [
                'name'   => 'mango_orange',
                'hex'    => '#FF8040',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_BRIGHT_ORANGE => [
                'name'   => 'bright_orange',
                'hex'    => '#FF6600',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],

            // 🟡 YELLOW
//            self::COLOR_PEACH_PUFF => [
//                'name'   => 'peach_puff',
//                'hex'    => '#FFDAB9',
//                'family' => ColorFamily::YELLOW,
//                'tone'   => ColorTone::LIGHT,
//                'temp'   => ColorTemperature::WARM,
//                'sat'    => ColorSaturation::SOFT
//            ],
            self::COLOR_MOCCASIN => [
                'name'   => 'moccasin',
                'hex'    => '#FFE4B5',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_LIGHT_GOLDEN => [
                'name'   => 'light_golden',
                'hex'    => '#FFE87C',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_PURE_YELLOW => [
                'name'   => 'pure_yellow',
                'hex'    => '#FFFF00',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_GOLD => [
                'name'   => 'gold',
                'hex'    => '#FFD700',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_BRIGHT_GOLD => [
                'name'   => 'bright_gold',
                'hex'    => '#FFC93C',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_GOLDENROD => [
                'name'   => 'goldenrod',
                'hex'    => '#DAA520',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_DARK_GOLDENROD => [
                'name'   => 'dark_goldenrod',
                'hex'    => '#B8860B',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],

            // 🟢 GREEN
            self::COLOR_LIGHT_GREEN => [
                'name'   => 'light_green',
                'hex'    => '#90EE90',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_PURE_GREEN => [
                'name'   => 'pure_green',
                'hex'    => '#00FF00',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_LIME => [
                'name'   => 'lime',
                'hex'    => '#32CD32',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_OLIVE_DRAB => [
                'name'   => 'olive_drab',
                'hex'    => '#6B8E23',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_FOREST => [
                'name'   => 'forest',
                'hex'    => '#228B22',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_SEA_GREEN => [
                'name'   => 'sea_green',
                'hex'    => '#2E8B57',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_OFFICE_GREEN => [
                'name'   => 'office_green',
                'hex'    => '#008000',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_DARK_OLIVE => [
                'name'   => 'dark_olive',
                'hex'    => '#556B2F',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],

            // 🔵 BLUE
            self::COLOR_LIGHT_BLUE => [
                'name'   => 'light_blue',
                'hex'    => '#ADD8E6',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_SKY_BLUE => [
                'name'   => 'sky_blue',
                'hex'    => '#87CEEB',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_DEEP_SKY_BLUE => [
                'name'   => 'deep_sky_blue',
                'hex'    => '#00BFFF',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_DODGER_BLUE => [
                'name'   => 'dodger_blue',
                'hex'    => '#1E90FF',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_CORNFLOWER => [
                'name'   => 'cornflower',
                'hex'    => '#6495ED',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_ROYAL_BLUE => [
                'name'   => 'royal_blue',
                'hex'    => '#4169E1',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_PURE_BLUE => [
                'name'   => 'pure_blue',
                'hex'    => '#0000FF',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_NAVY => [
                'name'   => 'navy',
                'hex'    => '#000080',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],

            // 🟣 PURPLE
            self::COLOR_THISTLE => [
                'name'   => 'thistle',
                'hex'    => '#D8BFD8',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_PLUM => [
                'name'   => 'plum',
                'hex'    => '#DDA0DD',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_MEDIUM_PURPLE => [
                'name'   => 'medium_purple',
                'hex'    => '#9370DB',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_MEDIUM_ORCHID => [
                'name'   => 'medium_orchid',
                'hex'    => '#BA55D3',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_DARK_ORCHID => [
                'name'   => 'dark_orchid',
                'hex'    => '#9932CC',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_DARK_VIOLET => [
                'name'   => 'dark_violet',
                'hex'    => '#9400D3',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_REBECCA_PURPLE => [
                'name'   => 'rebecca_purple',
                'hex'    => '#663399',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_PURE_PURPLE => [
                'name'   => 'pure_purple',
                'hex'    => '#800080',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],
        ];

        foreach ($colors as $reference => $data) {
            $hsv = $this->converter->hexToHsv($data['hex']);
            $rgb = $this->converter->hexToRgb($data['hex']);

            $color = new Color();
            $color->setName($data['name']);
            $color->setHexCode($data['hex']);
            $color->setFamily($data['family']);
            $color->setTone($data['tone']);
            $color->setTemperature($data['temp']);
            $color->setSaturation($data['sat']);
            $color->setR($rgb->r);
            $color->setG($rgb->g);
            $color->setB($rgb->b);
            $color->setH($hsv->h);
            $color->setS($hsv->s);
            $color->setV($hsv->v);
            $manager->persist($color);
            $this->addReference($reference, $color);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['default', 'test'];
    }
}
