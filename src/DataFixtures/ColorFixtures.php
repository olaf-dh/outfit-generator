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
    public const string COLOR_NAVY           = 'color-navy';
    public const string COLOR_MIDNIGHT_BLUE  = 'color-midnight-blue';
    public const string COLOR_DENIM          = 'color-denim';
    public const string COLOR_ROYAL_BLUE     = 'color-royal-blue';
    public const string COLOR_STEEL_BLUE     = 'color-steel-blue';
    public const string COLOR_BABY_BLUE      = 'color-baby-blue';
    public const string COLOR_SKY_BLUE       = 'color-sky-blue';
    public const string COLOR_AZURE          = 'color-azure';

    public const string COLOR_CRIMSON        = 'color-crimson';
    public const string COLOR_DEEP_RED       = 'color-deep-red';
    public const string COLOR_SOFT_RED       = 'color-soft-red';
    public const string COLOR_RUBY           = 'color-ruby';
    public const string COLOR_WINE           = 'color-wine';
    public const string COLOR_CORAL_RED      = 'color-coral-red';
    public const string COLOR_BRICK_RED      = 'color-brick-red';
    public const string COLOR_TOMATO         = 'color-tomato';

    public const string COLOR_MINT           = 'color-mint';
    public const string COLOR_FOREST         = 'color-forest';
    public const string COLOR_LIME           = 'color-lime';
    public const string COLOR_EMERALD        = 'color-emerald';
    public const string COLOR_OLIVE          = 'color-olive';
    public const string COLOR_JADE           = 'color-jade';
    public const string COLOR_SAGE           = 'color-sage';
    public const string COLOR_MOSS           = 'color-moss';

    public const string COLOR_LEMON          = 'color-lemon';
    public const string COLOR_MUSTARD        = 'color-mustard';
    public const string COLOR_BUTTER         = 'color-butter';
    public const string COLOR_GOLDEN         = 'color-golden';
    public const string COLOR_HONEY          = 'color-honey';
    public const string COLOR_CANARY         = 'color-canary';
    public const string COLOR_OCHRE          = 'color-ochre';
    public const string COLOR_SAND_YELLOW    = 'color-sand-yellow';

    public const string COLOR_BURNT_ORANGE   = 'color-burnt-orange';
    public const string COLOR_COPPER         = 'color-copper';
    public const string COLOR_RUST           = 'color-rust';
    public const string COLOR_TANGERINE      = 'color-tangerine';
    public const string COLOR_AMBER          = 'color-amber';
    public const string COLOR_ORANGE_PEEL    = 'color-orange-peel';
    public const string COLOR_PEACH          = 'color-peach';
    public const string COLOR_APRICOT        = 'color-apricot';

    public const string COLOR_ROSE           = 'color-rose';
    public const string COLOR_BLUSH          = 'color-blush';
    public const string COLOR_HOT_PINK       = 'color-hot-pink';
    public const string COLOR_DUSTY_PINK     = 'color-dusty-pink';
    public const string COLOR_SALMON_PINK    = 'color-salmon-pink';
    public const string COLOR_FUCHSIA        = 'color-fuchsia';
    public const string COLOR_BABY_PINK      = 'color-baby-pink';
    public const string COLOR_RASPBERRY      = 'color-raspberry';

    public const string COLOR_INDIGO         = 'color-indigo';
    public const string COLOR_EGGPLANT       = 'color-eggplant';
    public const string COLOR_MAUVE          = 'color-mauve';
    public const string COLOR_VIOLET         = 'color-violet';
    public const string COLOR_AMETHYST       = 'color-amethyst';
    public const string COLOR_LAVENDER       = 'color-lavender';
    public const string COLOR_PLUM           = 'color-plum';
    public const string COLOR_LILAC          = 'color-lilac';

    public const string COLOR_COFFEE         = 'color-coffee';
    public const string COLOR_CHOCOLATE      = 'color-chocolate';
    public const string COLOR_MOCHA          = 'color-mocha';
    public const string COLOR_WALNUT         = 'color-walnut';
    public const string COLOR_SEPIA          = 'color-sepia';
    public const string COLOR_BEIGE_BROWN    = 'color-beige-brown';
    public const string COLOR_CINNAMON       = 'color-cinnamon';
    public const string COLOR_CARAMEL        = 'color-caramel';

    public const string COLOR_SLATE          = 'color-slate';
    public const string COLOR_GRAPHITE_GRAY  = 'color-graphite-gray';
    public const string COLOR_STEEL          = 'color-steel';
    public const string COLOR_ASH_GRAY       = 'color-ash-gray';
    public const string COLOR_SMOKE          = 'color-smoke';
    public const string COLOR_DOVE_GRAY      = 'color-dove-gray';
    public const string COLOR_SILVER         = 'color-silver';
    public const string COLOR_MIST           = 'color-mist';

    public const string COLOR_WHITE          = 'color-white';
    public const string COLOR_SNOW           = 'color-snow';
    public const string COLOR_IVORY_WHITE    = 'color-ivory-white';
    public const string COLOR_MILK           = 'color-milk';
    public const string COLOR_CLOUD          = 'color-cloud';
    public const string COLOR_BONE           = 'color-bone';
    public const string COLOR_FROST          = 'color-frost';
    public const string COLOR_PEARL          = 'color-pearl';

    public const string COLOR_BLACK          = 'color-black';
    public const string COLOR_SOFT_BLACK     = 'color-soft-black';
    public const string COLOR_CHARCOAL       = 'color-charcoal';
    public const string COLOR_JET_BLACK      = 'color-jet-black';
    public const string COLOR_OFF_BLACK      = 'color-off-black';
    public const string COLOR_ASH_BLACK      = 'color-ash-black';
    public const string COLOR_SMOKY_BLACK    = 'color-smoky-black';
    public const string COLOR_GRAPHITE       = 'color-graphite';

    public const string COLOR_CREAM          = 'color-cream';
    public const string COLOR_SAND           = 'color-sand';
    public const string COLOR_IVORY          = 'color-ivory';
    public const string COLOR_ECRU           = 'color-ecru';
    public const string COLOR_LINEN          = 'color-linen';
    public const string COLOR_ALMOND         = 'color-almond';
    public const string COLOR_CHAMPAGNE      = 'color-champagne';
    public const string COLOR_OAT            = 'color-oat';

    public function __construct(private readonly ColorConverterService $converter)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $colors = [
            // 🔵 BLUE
            self::COLOR_NAVY => [
                'name'   => 'navy',
                'hex'    => '#000080',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_MIDNIGHT_BLUE => [
                'name'   => 'midnight_blue',
                'hex'    => '#191970',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_SKY_BLUE => [
                'name'   => 'sky-blue',
                'hex'    => '#87CEEB',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::LIGHT,
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
            self::COLOR_STEEL_BLUE => [
                'name'   => 'steel_blue',
                'hex'    => '#4682B4',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_BABY_BLUE => [
                'name'   => 'baby_blue',
                'hex'    => '#AFC6F2',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_DENIM => [
                'name'   => 'denim',
                'hex'    => '#1560BD',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_AZURE => [
                'name'   => 'azure',
                'hex'    => '#007FFF',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],

            // 🔴 RED
            self::COLOR_CRIMSON => [
                'name'   => 'crimson',
                'hex'    => '#DC143C',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_DEEP_RED => [
                'name'   => 'deep_red',
                'hex'    => '#8B0000',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MEDIUM
            ],
            self::COLOR_SOFT_RED => [
                'name'   => 'soft-red',
                'hex'    => '#F08080',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_RUBY => [
                'name'   => 'ruby',
                'hex'    => '#E0115F',
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
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_WINE => [
                'name'   => 'wine',
                'hex'    => '#722F37',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_CORAL_RED => [
                'name'   => 'coral-red',
                'hex'    => '#FF4040',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_TOMATO => [
                'name'   => 'tomato',
                'hex'    => '#ff6347',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],

            // 🟢 GREEN
            self::COLOR_MINT => [
                'name'   => 'mint',
                'hex'    => '#98FF98',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_FOREST => [
                'name'   => 'forest',
                'hex'    => '#228B22',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_MOSS => [
                'name'   => 'moss',
                'hex'    => '#8A9A5B',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_EMERALD => [
                'name'   => 'emerald',
                'hex'    => '#50C878',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_OLIVE => [
                'name'   => 'olive',
                'hex'    => '#808000',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_LIME => [
                'name'   => 'lime',
                'hex'    => '#BFFF00',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_SAGE => [
                'name'   => 'sage',
                'hex'    => '#9CAF88',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_JADE => [
                'name'   => 'jade',
                'hex'    => '#00A86B',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],

            // 🟡 YELLOW
            self::COLOR_MUSTARD => [
                'name'   => 'mustard',
                'hex'    => '#E1AD01',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_OCHRE => [
                'name'   => 'ochre',
                'hex'    => '#CC7722',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_HONEY => [
                'name'   => 'honey',
                'hex'    => '#D4AF37',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_GOLDEN => [
                'name'   => 'golden',
                'hex'    => '#FFD700',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_LEMON => [
                'name'   => 'lemon',
                'hex'    => '#FFF44F',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_BUTTER => [
                'name'   => 'butter',
                'hex'    => '#F6E27F',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_SAND_YELLOW => [
                'name'   => 'sand_yellow',
                'hex'    => '#E4D00A',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_CANARY => [
                'name'   => 'canary',
                'hex'    => '#FFFF99',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],

            // 🟠 ORANGE
            self::COLOR_BURNT_ORANGE => [
                'name'   => 'burnt_orange',
                'hex'    => '#CC5500',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_COPPER => [
                'name'   => 'copper',
                'hex'    => '#B87333',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_RUST => [
                'name'   => 'rust',
                'hex'    => '#B7410E',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_ORANGE_PEEL => [
                'name'   => 'orange-peel',
                'hex'    => '#FFA500',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_TANGERINE => [
                'name'   => 'tangerine',
                'hex'    => '#F28500',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_AMBER => [
                'name'   => 'amber',
                'hex'    => '#FFBF00',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_PEACH => [
                'name'   => 'peach',
                'hex'    => '#FFDAB9',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_APRICOT => [
                'name'   => 'apricot',
                'hex'    => '#FBCEB1',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],

            // 🩷 PINK
            self::COLOR_RASPBERRY => [
                'name'   => 'raspberry',
                'hex'    => '#E30B5C',
                'family' => ColorFamily::PINK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_ROSE => [
                'name'   => 'rose',
                'hex'    => '#FF007F',
                'family' => ColorFamily::PINK,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_HOT_PINK => [
                'name'   => 'hot_pink',
                'hex'    => '#FF69B4',
                'family' => ColorFamily::PINK,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_FUCHSIA => [
                'name'   => 'fuchsia',
                'hex'    => '#FF00FF',
                'family' => ColorFamily::PINK,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_BLUSH => [
                'name'   => 'blush',
                'hex'    => '#F4C2C2',
                'family' => ColorFamily::PINK,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_DUSTY_PINK => [
                'name'   => 'dusty_pink',
                'hex'    => '#DCAE96',
                'family' => ColorFamily::PINK,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_SALMON_PINK => [
                'name'   => 'salmon_pink',
                'hex'    => '#FF91A4',
                'family' => ColorFamily::PINK,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_BABY_PINK => [
                'name'   => 'baby_pink',
                'hex'    => '#F9C2D1',
                'family' => ColorFamily::PINK,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],

            // 🟣 PURPLE
            self::COLOR_INDIGO => [
                'name'   => 'indigo',
                'hex'    => '#4B0082',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_EGGPLANT => [
                'name'   => 'eggplant',
                'hex'    => '#311432',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_AMETHYST => [
                'name'   => 'amethyst',
                'hex'    => '#9966CC',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_VIOLET => [
                'name'   => 'violet',
                'hex'    => '#8F00FF',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_PLUM => [
                'name'   => 'plum',
                'hex'    => '#8E4585',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_LAVENDER => [
                'name'   => 'lavender',
                'hex'    => '#E6E6FA',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_LILAC => [
                'name'   => 'lilac',
                'hex'    => '#C8A2C8',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_MAUVE => [
                'name'   => 'mauve',
                'hex'    => '#E0B0ff',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::SOFT
            ],

            // 🟤 BROWN
            self::COLOR_COFFEE => [
                'name'   => 'coffee',
                'hex'    => '#4B2E2A',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_CHOCOLATE => [
                'name'   => 'chocolate',
                'hex'    => '#7B3F00',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_MOCHA => [
                'name'   => 'mocha',
                'hex'    => '#6F4E37',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_WALNUT => [
                'name'   => 'walnut',
                'hex'    => '#5C4033',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_SEPIA => [
                'name'   => 'sepia',
                'hex'    => '#704214',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_CINNAMON => [
                'name'   => 'cinnamon',
                'hex'    => '#D2691E',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIBRANT
            ],
            self::COLOR_CARAMEL => [
                'name'   => 'caramel',
                'hex'    => '#C68E17',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_BEIGE_BROWN => [
                'name'   => 'beige-brown',
                'hex'    => '#D2B48C',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],

            // ⚫ GRAY
            self::COLOR_SLATE => [
                'name'   => 'slate',
                'hex'    => '#708090',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_GRAPHITE_GRAY => [
                'name'   => 'graphite_gray',
                'hex'    => '#474A51',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_STEEL => [
                'name'   => 'steel',
                'hex'    => '#71797E',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_SMOKE => [
                'name'   => 'smoke',
                'hex'    => '#848884',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_ASH_GRAY => [
                'name'   => 'ash_gray',
                'hex'    => '#B2BEB5',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_DOVE_GRAY => [
                'name'   => 'dove_gray',
                'hex'    => '#D3D3D3',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_SILVER => [
                'name'   => 'silver',
                'hex'    => '#C0C0C0',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_MIST => [
                'name'   => 'mist',
                'hex'    => '#E3E3E3',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::SOFT
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
            self::COLOR_SNOW => [
                'name'   => 'snow',
                'hex'    => '#FFFAFA',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_IVORY_WHITE => [
                'name'   => 'ivory_white',
                'hex'    => '#FFFFF0',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_MILK => [
                'name'   => 'milk',
                'hex'    => '#FBFBF7',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_CLOUD => [
                'name'   => 'cloud',
                'hex'    => '#F5F5F5',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_BONE => [
                'name'   => 'bone',
                'hex'    => '#EDE6D6',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_FROST => [
                'name'   => 'frost',
                'hex'    => '#F0F8FF',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_PEARL => [
                'name'   => 'pearl',
                'hex'    => '#F8F6F0',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::SOFT
            ],

            // ⚫ BLACK
            self::COLOR_BLACK => [
                'name'   => 'black',
                'hex'    => '#000000',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_JET_BLACK => [
                'name'   => 'jet_black',
                'hex'    => '#0A0A0A',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_CHARCOAL => [
                'name'   => 'charcoal',
                'hex'    => '#36454F',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_SOFT_BLACK => [
                'name'   => 'soft_black',
                'hex'    => '#1C1C1C',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_GRAPHITE => [
                'name'   => 'graphite',
                'hex'    => '#2B2B2B',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_OFF_BLACK => [
                'name'   => 'off_black',
                'hex'    => '#121212',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_ASH_BLACK => [
                'name'   => 'ash_black',
                'hex'    => '#3A3A3A',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_SMOKY_BLACK => [
                'name'   => 'smoky_black',
                'hex'    => '#2F2F2F',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],

            // 🟫 BEIGE
            self::COLOR_CREAM => [
                'name'   => 'cream',
                'hex'    => '#FFFDD0',
                'family' => ColorFamily::BEIGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_SAND => [
                'name'   => 'sand',
                'hex'    => '#C2B280',
                'family' => ColorFamily::BEIGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_IVORY => [
                'name'   => 'ivory',
                'hex'    => '#FFFFF0',
                'family' => ColorFamily::BEIGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_ECRU => [
                'name'   => 'ecru',
                'hex'    => '#CDB891',
                'family' => ColorFamily::BEIGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_LINEN => [
                'name'   => 'linen',
                'hex'    => '#E9DCC9',
                'family' => ColorFamily::BEIGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_ALMOND => [
                'name'   => 'almond',
                'hex'    => '#EFDECD',
                'family' => ColorFamily::BEIGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_CHAMPAGNE => [
                'name'   => 'champagne',
                'hex'    => '#F7E7CE',
                'family' => ColorFamily::BEIGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::SOFT
            ],
            self::COLOR_OAT => [
                'name'   => 'oat',
                'hex'    => '#D8CAB8',
                'family' => ColorFamily::BEIGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
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
            $color->setR($rgb['r']);
            $color->setG($rgb['g']);
            $color->setB($rgb['b']);
            $color->setH($hsv['h']);
            $color->setS($hsv['s']);
            $color->setV($hsv['v']);
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
