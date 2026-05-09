<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\Outfit\Enum\ColorFamily;
use App\Domain\Outfit\Enum\ColorSaturation;
use App\Domain\Outfit\Enum\ColorTemperature;
use App\Domain\Outfit\Enum\ColorTone;
use App\Entity\Color;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ColorFixtures extends Fixture
{
    // Colors
    public const string COLOR_NAVY           = 'color-navy';
    public const string COLOR_DARK_BLUE      = 'color-dark-blue';
    public const string COLOR_BLUE           = 'color-blue';
    public const string COLOR_ROYAL_BLUE     = 'color-royal-blue';
    public const string COLOR_STEEL_BLUE     = 'color-steel-blue';
    public const string COLOR_LIGHT_BLUE     = 'color-light-blue';
    public const string COLOR_DUSTY_BLUE     = 'color-dusty-blue';
    public const string COLOR_BURGUNDY       = 'color-burgundy';
    public const string COLOR_DARK_RED       = 'color-dark-red';
    public const string COLOR_RED            = 'color-red';
    public const string COLOR_CHERRY         = 'color-cherry';
    public const string COLOR_DUSTY_RED      = 'color-dusty-red';
    public const string COLOR_LIGHT_RED      = 'color-light-red';
    public const string COLOR_BRICK          = 'color-brick';
    public const string COLOR_DARK_GREEN     = 'color-dark-green';
    public const string COLOR_FOREST_GREEN   = 'color-forest-green';
    public const string COLOR_GREEN          = 'color-green';
    public const string COLOR_EMERALD        = 'color-emerald';
    public const string COLOR_OLIVE          = 'color-olive';
    public const string COLOR_LIGHT_GREEN    = 'color-light-green';
    public const string COLOR_SAGE           = 'color-sage';
    public const string COLOR_YELLOW         = 'color-yellow';
    public const string COLOR_MUSTARD        = 'color-mustard';
    public const string COLOR_DARK_YELLOW    = 'color-dark-yellow';
    public const string COLOR_GOLDEN         = 'color-golden';
    public const string COLOR_DUSTY_YELLOW   = 'color-dusty-yellow';
    public const string COLOR_LIGHT_YELLOW   = 'color-light-yellow';
    public const string COLOR_SAND_YELLOW   = 'color-sand-yellow';
    public const string COLOR_BURNT_ORANGE   = 'color-burnt-orange';
    public const string COLOR_DARK_ORANGE    = 'color-dark-orange';
    public const string COLOR_ORANGE         = 'color-orange';
    public const string COLOR_CORAL          = 'color-coral';
    public const string COLOR_DUSTY_ORANGE   = 'color-dusty-orange';
    public const string COLOR_LIGHT_ORANGE   = 'color-light-orange';
    public const string COLOR_TERRACOTTA     = 'color-terracotta';
    public const string COLOR_DARK_PURPLE    = 'color-dark-purple';
    public const string COLOR_EGGPLANT       = 'color-eggplant';
    public const string COLOR_PURPLE         = 'color-purple';
    public const string COLOR_VIOLET         = 'color-violet';
    public const string COLOR_DUSTY_PURPLE   = 'color-dusty-purple';
    public const string COLOR_LAVENDER       = 'color-lavender';
    public const string COLOR_PLUM           = 'color-plum';
    public const string COLOR_DARK_BROWN     = 'color-dark-brown';
    public const string COLOR_CHOCOLATE      = 'color-chocolate';
    public const string COLOR_BROWN          = 'color-brown';
    public const string COLOR_CHESTNUT       = 'color-chestnut';
    public const string COLOR_TAUPE          = 'color-taupe';
    public const string COLOR_LIGHT_BROWN    = 'color-light-brown';
    public const string COLOR_CAMEL          = 'color-camel';
    public const string COLOR_CHARCOAL       = 'color-charcoal';
    public const string COLOR_DARK_GRAY      = 'color-dark-gray';
    public const string COLOR_GRAY           = 'color-gray';
    public const string COLOR_COOL_GRAY      = 'color-cool-gray';
    public const string COLOR_WARM_GRAY      = 'color-warm-gray';
    public const string COLOR_LIGHT_GRAY     = 'color-light-gray';
    public const string COLOR_SILVER         = 'color-silver';
    public const string COLOR_WHITE          = 'color-white';
    public const string COLOR_OFF_WHITE      = 'color-off-white';
    public const string COLOR_IVORY          = 'color-ivory';
    public const string COLOR_CREAM          = 'color-cream';
    public const string COLOR_COOL_WHITE     = 'color-cool-white';
    public const string COLOR_SOFT_WHITE     = 'color-soft-white';
    public const string COLOR_PEARL          = 'color-pearl';
    public const string COLOR_BLACK          = 'color-black';
    public const string COLOR_SOFT_BLACK     = 'color-soft-black';
    public const string COLOR_CHARCOAL_BLACK = 'color-charcoal-black';
    public const string COLOR_WARM_BLACK     = 'color-warm-black';
    public const string COLOR_FADED_BLACK    = 'color-faded-black';
    public const string COLOR_COOL_BLACK     = 'color-cool-black';
    public const string COLOR_MATTE_BLACK    = 'color-matte-black';
    public const string COLOR_BEIGE          = 'color-beige';
    public const string COLOR_SAND           = 'color-sand';
    public const string COLOR_NUDE           = 'color-nude';
    public const string COLOR_TAUPE_LIGHT    = 'color-taupe-light';
    public const string COLOR_STONE          = 'color-stone';
    public const string COLOR_WARM_BEIGE     = 'color-warm-beige';
    public const string COLOR_COOL_BEIGE     = 'color-cool-beige';

    public function load(ObjectManager $manager): void
    {
        $colors = [
            // 🔵 BLUE
            self::COLOR_NAVY => [
                'name'   => 'navy',
                'hex'    => '#1B2A4A',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_DARK_BLUE => [
                'name'   => 'dark_blue',
                'hex'    => '#1E3A8A',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIVID
            ],
            self::COLOR_BLUE => [
                'name'   => 'blue',
                'hex'    => '#2563EB',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIVID
            ],
            self::COLOR_ROYAL_BLUE => [
                'name'   => 'royal_blue',
                'hex'    => '#3B82F6',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIVID
            ],
            self::COLOR_STEEL_BLUE => [
                'name'   => 'steel_blue',
                'hex'    => '#4682B4',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_LIGHT_BLUE => [
                'name'   => 'light_blue',
                'hex'    => '#93C5FD',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_DUSTY_BLUE => [
                'name'   => 'dusty_blue',
                'hex'    => '#7A8FA6',
                'family' => ColorFamily::BLUE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::MUTED
            ],

            // 🔴 RED
            self::COLOR_BURGUNDY => [
                'name'   => 'burgundy',
                'hex'    => '#800020',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_DARK_RED => [
                'name'   => 'dark_red',
                'hex'    => '#7F1D1D',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIVID
            ],
            self::COLOR_RED => [
                'name'   => 'red',
                'hex'    => '#DC2626',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIVID
            ],
            self::COLOR_CHERRY => [
                'name'   => 'cherry',
                'hex'    => '#EF4444',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIVID
            ],
            self::COLOR_BRICK => [
                'name'   => 'brick_red',
                'hex'    => '#B91C1C',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_LIGHT_RED => [
                'name'   => 'light_red',
                'hex'    => '#FCA5A5',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_DUSTY_RED => [
                'name'   => 'rose',
                'hex'    => '#A8716E',
                'family' => ColorFamily::RED,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],

            // 🟢 GREEN
            self::COLOR_DARK_GREEN => [
                'name'   => 'dark_green',
                'hex'    => '#14532D',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIVID
            ],
            self::COLOR_FOREST_GREEN => [
                'name'   => 'forest_green',
                'hex'    => '#166534',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_GREEN => [
                'name'   => 'green',
                'hex'    => '#16A34A',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIVID
            ],
            self::COLOR_EMERALD => [
                'name'   => 'emerald',
                'hex'    => '#10B981',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIVID
            ],
            self::COLOR_OLIVE => [
                'name'   => 'olive',
                'hex'    => '#808000',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_LIGHT_GREEN => [
                'name'   => 'light_green',
                'hex'    => '#86EFAC',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_SAGE => [
                'name'   => 'sage',
                'hex'    => '#A3B18A',
                'family' => ColorFamily::GREEN,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::MUTED
            ],

            // 🟡 YELLOW
            self::COLOR_MUSTARD => [
                'name'   => 'mustard',
                'hex'    => '#B45309',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_DARK_YELLOW => [
                'name'   => 'dark_yellow',
                'hex'    => '#A16207',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIVID
            ],
            self::COLOR_YELLOW => [
                'name'   => 'yellow',
                'hex'    => '#FACC15',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIVID
            ],
            self::COLOR_GOLDEN => [
                'name'   => 'golden',
                'hex'    => '#EAB308',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIVID
            ],
            self::COLOR_DUSTY_YELLOW => [
                'name'   => 'dusty_yellow',
                'hex'    => '#D6C88A',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_LIGHT_YELLOW => [
                'name'   => 'light_yellow',
                'hex'    => '#FEF08A',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_SAND_YELLOW => [
                'name'   => 'sand_yellow',
                'hex'    => '#D4C27C',
                'family' => ColorFamily::YELLOW,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL
            ],

            // 🟠 ORANGE
            self::COLOR_BURNT_ORANGE => [
                'name'   => 'burnt_orange',
                'hex'    => '#9A3412',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_DARK_ORANGE => [
                'name'   => 'dark_orange',
                'hex'    => '#C2410C',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIVID
            ],
            self::COLOR_ORANGE => [
                'name'   => 'orange',
                'hex'    => '#F97316',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIVID
            ],
            self::COLOR_CORAL => [
                'name'   => 'coral',
                'hex'    => '#FB7185',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIVID
            ],
            self::COLOR_TERRACOTTA => [
                'name'   => 'terracotta',
                'hex'    => '#E07A5F',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_LIGHT_ORANGE => [
                'name'   => 'light_orange',
                'hex'    => '#FDBA74',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_DUSTY_ORANGE => [
                'name'   => 'dusty_orange',
                'hex'    => '#D6A77A',
                'family' => ColorFamily::ORANGE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],

            // 🟣 PURPLE
            self::COLOR_DARK_PURPLE => [
                'name'   => 'dark_purple',
                'hex'    => '#4C1D95',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIVID
            ],
            self::COLOR_EGGPLANT => [
                'name'   => 'eggplant',
                'hex'    => '#5B2C6F',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_PURPLE => [
                'name'   => 'purple',
                'hex'    => '#7C3AED',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIVID
            ],
            self::COLOR_VIOLET => [
                'name'   => 'violet',
                'hex'    => '#8B5CF6',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIVID
            ],
            self::COLOR_PLUM => [
                'name'   => 'plum',
                'hex'    => '#7E5A9B',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_LAVENDER => [
                'name'   => 'lavender',
                'hex'    => '#C4B5FD',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_DUSTY_PURPLE => [
                'name'   => 'dusty_purple',
                'hex'    => '#9A86A4',
                'family' => ColorFamily::PURPLE,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::MUTED
            ],

            // 🟤 BROWN
            self::COLOR_DARK_BROWN => [
                'name'   => 'dark_brown',
                'hex'    => '#3E2723',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_CHOCOLATE => [
                'name'   => 'chocolate',
                'hex'    => '#5D4037',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIVID
            ],
            self::COLOR_BROWN => [
                'name'   => 'brown',
                'hex'    => '#8D6E63',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_CHESTNUT => [
                'name'   => 'chestnut',
                'hex'    => '#A0522D',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::VIVID
            ],
            self::COLOR_TAUPE => [
                'name'   => 'taupe',
                'hex'    => '#A1887F',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_LIGHT_BROWN => [
                'name'   => 'light_brown',
                'hex'    => '#D7CCC8',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_CAMEL => [
                'name'   => 'camel',
                'hex'    => '#C19A6B',
                'family' => ColorFamily::BROWN,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL
            ],

            // ⚪ GRAY
            self::COLOR_CHARCOAL => [
                'name'   => 'anthracite',
                'hex'    => '#374151',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_DARK_GRAY => [
                'name'   => 'dark_gray',
                'hex'    => '#4B5563',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_GRAY => [
                'name'   => 'gray',
                'hex'    => '#9CA3AF',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_COOL_GRAY => [
                'name'   => 'cool_gray',
                'hex'    => '#94A3B8',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_WARM_GRAY => [
                'name'   => 'warm_gray',
                'hex'    => '#A8A29E',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::MEDIUM,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_LIGHT_GRAY => [
                'name'   => 'light_gray',
                'hex'    => '#E5E7EB',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_SILVER => [
                'name'   => 'silver',
                'hex'    => '#D1D5DB',
                'family' => ColorFamily::GRAY,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::VIVID
            ],

            // ⚪ WHITE
            self::COLOR_WHITE => [
                'name'   => 'white',
                'hex'    => '#FFFFFF',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_OFF_WHITE => [
                'name'   => 'off_white',
                'hex'    => '#F9FAFB',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_IVORY => [
                'name'   => 'ivory',
                'hex'    => '#FFFFF0',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL]
            ,
            self::COLOR_CREAM => [
                'name'   => 'cream',
                'hex'    => '#FFFDD0',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_SOFT_WHITE => [
                'name'   => 'soft_white',
                'hex'    => '#F5F5F5',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_COOL_WHITE => [
                'name'   => 'cool_white',
                'hex'    => '#F0F9FF',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_PEARL => [
                'name'   => 'pearl',
                'hex'    => '#F8F6F0',
                'family' => ColorFamily::WHITE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],

            // ⚫ BLACK
            self::COLOR_BLACK => [
                'name'   => 'black',
                'hex'    => '#000000',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_SOFT_BLACK => [
                'name'   => 'soft_black',
                'hex'    => '#1F2937',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_CHARCOAL_BLACK => [
                'name'   => 'charcoal_black',
                'hex'    => '#111827',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_WARM_BLACK => [
                'name'   => 'warm_black',
                'hex'    => '#2D1B14',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_FADED_BLACK => [
                'name'   => 'faded_black',
                'hex'    => '#374151',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_COOL_BLACK => [
                'name'   => 'cool_black',
                'hex'    => '#0F172A',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_MATTE_BLACK => [
                'name'   => 'matte_black',
                'hex'    => '#121212',
                'family' => ColorFamily::BLACK,
                'tone'   => ColorTone::DARK,
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
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_SAND => [
                'name'   => 'sand',
                'hex'    => '#EADBC8',
                'family' => ColorFamily::BEIGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_NUDE => [
                'name'   => 'nude',
                'hex'    => '#E3BC9A',
                'family' => ColorFamily::BEIGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_TAUPE_LIGHT => [
                'name'   => 'taupe_light',
                'hex'    => '#D2B48C',
                'family' => ColorFamily::BEIGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::NORMAL
            ],
            self::COLOR_STONE => [
                'name'   => 'stone',
                'hex'    => '#D6D3D1',
                'family' => ColorFamily::BEIGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::NEUTRAL,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_WARM_BEIGE => [
                'name'   => 'warm_beige',
                'hex'    => '#E8D3B9',
                'family' => ColorFamily::BEIGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::WARM,
                'sat'    => ColorSaturation::MUTED
            ],
            self::COLOR_COOL_BEIGE => [
                'name'   => 'cool_beige',
                'hex'    => '#D8D2C2',
                'family' => ColorFamily::BEIGE,
                'tone'   => ColorTone::LIGHT,
                'temp'   => ColorTemperature::COOL,
                'sat'    => ColorSaturation::MUTED
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

        $manager->flush();
    }
}
