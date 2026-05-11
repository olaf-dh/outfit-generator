<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\Outfit\Enum\ClothingItemStatus;
use App\Entity\ClothingItem;
use App\Entity\Color;
use App\Entity\ItemColor;
use App\Entity\ItemMaterial;
use App\Entity\Material;
use App\Entity\Pattern;
use App\Entity\Season;
use App\Entity\Style;
use App\Entity\SubCategory;
use App\Entity\User;
use App\Entity\WeatherCondition;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClothingItemFixtures extends Fixture implements DependentFixtureInterface
{
    public const string WHITE_SHIRT       = 'item-white-shirt';
    public const string RED_PULLOVER      = 'item-red-pullover';
    public const string BLUE_STRIPE_SHIRT = 'item-blue-stripe-shirt';
    public const string ANTHRACITE_PANTS  = 'item-anthracite-pants';
    public const string MUSTARD_CHINO     = 'item-mustard-chino';
    public const string DARK_BLUE_JEANS   = 'item-dark-blue-jeans';
    public const string GRAY_COAT         = 'item-gray-coat';
    public const string BEIGE_TRENCH      = 'item-beige-trench';
    public const string CAMEL_SHOES       = 'item-camel-shoes';
    public const string WHITE_SNEAKERS    = 'item-white-sneakers';
    public const string GRAY_SCARF        = 'item-gray-scarf';
    public const string BROWN_BELT        = 'item-brown-belt';

    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        /* ---------- Owner ---------- */
        $owner = new User();
        $owner->setEmail('user@example.com');
        $owner->setRoles(['ROLE_USER']);
        $owner->setFirstName('User');
        $owner->setPassword($this->hasher->hashPassword($owner, 'test1234'));
        $owner->setIsVerified(true);
        $manager->persist($owner);
        $this->addReference('user_demo', $owner);

        $manager->flush();

        $status = ClothingItemStatus::COMPLETE;

        foreach ($this->getItemDefinitions() as $reference => $data) {
            $item = new ClothingItem();
            $item->setName($data['name']);
            $item->setSubCategory($this->getReference($data['subcategory'], SubCategory::class));
            $item->setMinLayerDepth($data['min_layer']);
            $item->setMaxLayerDepth($data['max_layer']);
            $item->setStatus($status);
            $item->setOwner($owner);

            if (isset($data['notes'])) {
                $item->setNotes($data['notes']);
            }

            // Primary color
            $primaryColor = $this->getReference($data['primary_color'], Color::class);
            $item->addItemColor(new ItemColor($item, $primaryColor, true));

            // Secondary colors
            foreach ($data['secondary_colors'] ?? [] as $colorRef) {
                $color = $this->getReference($colorRef, Color::class);
                $item->addItemColor(new ItemColor($item, $color, false));
            }

            // Materials
            foreach ($data['materials'] as $materialRef => $percentage) {
                $material = $this->getReference($materialRef, Material::class);
                $itemMaterial = new ItemMaterial();
                $itemMaterial->setMaterial($material);
                $itemMaterial->setPercentage($percentage);
                $itemMaterial->setClothingItem($item);
                $item->addItemMaterial($itemMaterial);
            }

            // Pattern, Style, Season, WeatherCondition
            foreach ($data['patterns'] as $ref) {
                $item->addPattern($this->getReference($ref, Pattern::class));
            }

            foreach ($data['styles'] as $ref) {
                $item->addStyle($this->getReference($ref, Style::class));
            }

            foreach ($data['seasons'] as $ref) {
                $item->addSeason($this->getReference($ref, Season::class));
            }

            foreach ($data['weather_conditions'] ?? [] as $ref) {
                $item->addWeatherCondition($this->getReference($ref, WeatherCondition::class));
            }

            $manager->persist($item);
            $this->addReference($reference, $item);
        }

        $manager->flush();
    }

    /**
     * @return array<string, array{
     *     name: string,
     *     subcategory: string,
     *     min_layer: int,
     *     max_layer: int,
     *     primary_color: string,
     *     secondary_colors?: string[],
     *     materials: array<string, float>,
     *     patterns: string[],
     *     styles: string[],
     *     seasons: string[],
     *     weather_conditions?: string[],
     *     notes?: string
     * }>
     */
    private function getItemDefinitions(): array
    {
        return [
            // --- Upper body ---
            self::WHITE_SHIRT => [
                'name'          => 'White Business Shirt',
                'subcategory'   => SubCategoryFixtures::BUTTON_DOWN,
                'min_layer'     => 2,
                'max_layer'     => 2,
                'primary_color' => ColorFixtures::COLOR_WHITE,
                'materials'     => [MaterialFixtures::MATERIAL_COTTON => 100.0],
                'patterns'      => [LookupFixtures::PATTERN_SOLID],
                'styles'        => [LookupFixtures::STYLE_BUSINESS, LookupFixtures::STYLE_SMART_CASUAL],
                'seasons'       => [
                    LookupFixtures::SEASON_SPRING,
                    LookupFixtures::SEASON_SUMMER,
                    LookupFixtures::SEASON_AUTUMN
                ],
            ],
            self::RED_PULLOVER => [
                'name'          => 'Red Cashmere Pullover',
                'subcategory'   => SubCategoryFixtures::PULLOVER,
                'min_layer'     => 2,
                'max_layer'     => 3,
                'primary_color' => ColorFixtures::COLOR_RED,
                'materials'     => [MaterialFixtures::MATERIAL_CASHMERE => 100.0],
                'patterns'      => [LookupFixtures::PATTERN_SOLID],
                'styles'        => [LookupFixtures::STYLE_SMART_CASUAL, LookupFixtures::STYLE_CASUAL],
                'seasons'       => [LookupFixtures::SEASON_AUTUMN, LookupFixtures::SEASON_WINTER],
            ],
            self::BLUE_STRIPE_SHIRT => [
                'name'             => 'Blue Stripe Shirt',
                'subcategory'      => SubCategoryFixtures::BUTTON_DOWN,
                'min_layer'        => 2,
                'max_layer'        => 2,
                'primary_color'    => ColorFixtures::COLOR_DARK_BLUE,
                'secondary_colors' => [ColorFixtures::COLOR_WHITE],
                'materials'        => [MaterialFixtures::MATERIAL_COTTON => 100.0],
                'patterns'         => [LookupFixtures::PATTERN_VERTICAL_STRIPES],
                'styles'           => [LookupFixtures::STYLE_SMART_CASUAL, LookupFixtures::STYLE_CASUAL],
                'seasons'          => [LookupFixtures::SEASON_SPRING, LookupFixtures::SEASON_SUMMER],
            ],

            // --- Lower body ---
            self::ANTHRACITE_PANTS => [
                'name'          => 'Anthracite Pants',
                'subcategory'   => SubCategoryFixtures::TROUSERS,
                'min_layer'     => 1,
                'max_layer'     => 1,
                'primary_color' => ColorFixtures::COLOR_CHARCOAL,
                'materials'     => [MaterialFixtures::MATERIAL_WOOL => 100.0],
                'patterns'      => [LookupFixtures::PATTERN_SOLID],
                'styles'        => [LookupFixtures::STYLE_BUSINESS, LookupFixtures::STYLE_SMART_CASUAL],
                'seasons'       => [
                    LookupFixtures::SEASON_SPRING,
                    LookupFixtures::SEASON_AUTUMN,
                    LookupFixtures::SEASON_WINTER
                ],
            ],
            self::MUSTARD_CHINO => [
                'name'          => 'Mustard Chino',
                'subcategory'   => SubCategoryFixtures::CHINO,
                'min_layer'     => 1,
                'max_layer'     => 1,
                'primary_color' => ColorFixtures::COLOR_MUSTARD,
                'materials'     => [MaterialFixtures::MATERIAL_COTTON => 100.0],
                'patterns'      => [LookupFixtures::PATTERN_SOLID],
                'styles'        => [LookupFixtures::STYLE_SMART_CASUAL, LookupFixtures::STYLE_CASUAL],
                'seasons'       => [LookupFixtures::SEASON_SPRING, LookupFixtures::SEASON_AUTUMN],
            ],
            self::DARK_BLUE_JEANS => [
                'name'          => 'Dark Blue Jeans',
                'subcategory'   => SubCategoryFixtures::JEANS,
                'min_layer'     => 1,
                'max_layer'     => 1,
                'primary_color' => ColorFixtures::COLOR_DARK_BLUE,
                'materials'     => [MaterialFixtures::MATERIAL_DENIM => 100.0],
                'patterns'      => [LookupFixtures::PATTERN_SOLID],
                'styles'        => [LookupFixtures::STYLE_CASUAL, LookupFixtures::STYLE_SMART_CASUAL],
                'seasons'       => [], // no Season = universal
            ],

            // --- Outer layer ---
            self::GRAY_COAT => [
                'name'          => 'Gray Wool Coat',
                'subcategory'   => SubCategoryFixtures::COAT,
                'min_layer'     => 4,
                'max_layer'     => 5,
                'primary_color' => ColorFixtures::COLOR_WARM_GRAY,
                'materials'     => [MaterialFixtures::MATERIAL_WOOL => 100.0],
                'patterns'      => [LookupFixtures::PATTERN_SOLID],
                'styles'        => [LookupFixtures::STYLE_BUSINESS, LookupFixtures::STYLE_SMART_CASUAL],
                'seasons'       => [LookupFixtures::SEASON_AUTUMN, LookupFixtures::SEASON_WINTER],
            ],
            self::BEIGE_TRENCH => [
                'name'               => 'Beige Trenchcoat',
                'subcategory'        => SubCategoryFixtures::TRENCH_COAT,
                'min_layer'          => 4,
                'max_layer'          => 5,
                'primary_color'      => ColorFixtures::COLOR_BEIGE,
                'materials'          => [
                    MaterialFixtures::MATERIAL_COTTON    => 65.0,
                    MaterialFixtures::MATERIAL_POLYESTER => 35.0,
                ],
                'patterns'           => [LookupFixtures::PATTERN_SOLID],
                'styles'             => [LookupFixtures::STYLE_SMART_CASUAL, LookupFixtures::STYLE_BUSINESS],
                'seasons'            => [LookupFixtures::SEASON_SPRING, LookupFixtures::SEASON_AUTUMN],
                'weather_conditions' => [LookupFixtures::WEATHER_RAINY, LookupFixtures::WEATHER_WINDY],
            ],

            // --- Shoes ---
            self::CAMEL_SHOES => [
                'name'          => 'Camel Leather Shoes',
                'subcategory'   => SubCategoryFixtures::BUSINESS_SHOES,
                'min_layer'     => 1,
                'max_layer'     => 1,
                'primary_color' => ColorFixtures::COLOR_CAMEL,
                'materials'     => [MaterialFixtures::MATERIAL_LEATHER => 100.0],
                'patterns'      => [LookupFixtures::PATTERN_SOLID],
                'styles'        => [LookupFixtures::STYLE_SMART_CASUAL, LookupFixtures::STYLE_BUSINESS],
                'seasons'       => [],
            ],
            self::WHITE_SNEAKERS => [
                'name'          => 'White Sneaker',
                'subcategory'   => SubCategoryFixtures::SNEAKER,
                'min_layer'     => 1,
                'max_layer'     => 1,
                'primary_color' => ColorFixtures::COLOR_WHITE,
                'materials'     => [MaterialFixtures::MATERIAL_COTTON => 100.0],
                'patterns'      => [LookupFixtures::PATTERN_SOLID],
                'styles'        => [LookupFixtures::STYLE_CASUAL],
                'seasons'       => [],
            ],

            // --- Accessory ---
            self::GRAY_SCARF => [
                'name'               => 'Gray Wool Scarf',
                'subcategory'        => SubCategoryFixtures::SCARF,
                'min_layer'          => 1,
                'max_layer'          => 1,
                'primary_color'      => ColorFixtures::COLOR_LIGHT_GRAY,
                'materials'          => [MaterialFixtures::MATERIAL_WOOL => 100.0],
                'patterns'           => [LookupFixtures::PATTERN_SOLID],
                'styles'             => [LookupFixtures::STYLE_CASUAL, LookupFixtures::STYLE_SMART_CASUAL],
                'seasons'            => [LookupFixtures::SEASON_AUTUMN, LookupFixtures::SEASON_WINTER],
                'weather_conditions' => [LookupFixtures::WEATHER_COLD, LookupFixtures::WEATHER_WINDY],
            ],
            self::BROWN_BELT => [
                'name'          => 'Brown Leather Belt',
                'subcategory'   => SubCategoryFixtures::BELT,
                'min_layer'     => 1,
                'max_layer'     => 1,
                'primary_color' => ColorFixtures::COLOR_TAUPE,
                'materials'     => [MaterialFixtures::MATERIAL_LEATHER => 100.0],
                'patterns'      => [LookupFixtures::PATTERN_SOLID],
                'styles'        => [
                    LookupFixtures::STYLE_CASUAL,
                    LookupFixtures::STYLE_SMART_CASUAL,
                    LookupFixtures::STYLE_BUSINESS]
                ,
                'seasons'       => [],
            ],
        ];
    }

    public function getDependencies(): array
    {
        return [
            ColorFixtures::class,
            MaterialFixtures::class,
            SubCategoryFixtures::class,
            LookupFixtures::class,
        ];
    }
}
