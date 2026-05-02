<?php

declare(strict_types=1);

namespace App\DataFixtures;

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
    public const string COGNAC_CHINO      = 'item-cognac-chino';
    public const string DARK_BLUE_JEANS   = 'item-dark-blue-jeans';
    public const string GRAY_COAT         = 'item-gray-coat';
    public const string BEIGE_TRENCH      = 'item-beige-trench';
    public const string COGNAC_SHOES      = 'item-cognac-shoes';
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

        foreach ($this->getItemDefinitions() as $reference => $data) {
            $item = new ClothingItem();
            $item->setName($data['name']);
            $item->setSubCategory($this->getReference($data['subcategory'], SubCategory::class));
            $item->setMinLayerDepth($data['min_layer']);
            $item->setMaxLayerDepth($data['max_layer']);
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
                $item->addItemMaterial(new ItemMaterial($item, $material, $percentage));
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
                'name'        => 'Weißes Businesshemd',
                'subcategory' => SubCategoryFixtures::BUTTON_DOWN,
                'min_layer'   => 2,
                'max_layer'   => 2,
                'primary_color' => LookupFixtures::COLOR_WHITE,
                'materials'   => [LookupFixtures::MATERIAL_COTTON => 100.0],
                'patterns'    => [LookupFixtures::PATTERN_SOLID],
                'styles'      => [LookupFixtures::STYLE_BUSINESS, LookupFixtures::STYLE_SMART_CASUAL],
                'seasons'     => [
                    LookupFixtures::SEASON_SPRING,
                    LookupFixtures::SEASON_SUMMER,
                    LookupFixtures::SEASON_AUTUMN
                ],
            ],
            self::RED_PULLOVER => [
                'name'          => 'Roter Cashmerepullover',
                'subcategory'   => SubCategoryFixtures::PULLOVER,
                'min_layer'     => 2,
                'max_layer'     => 3,
                'primary_color' => LookupFixtures::COLOR_RED_VIVID,
                'materials'     => [LookupFixtures::MATERIAL_CASHMERE => 100.0],
                'patterns'      => [LookupFixtures::PATTERN_SOLID],
                'styles'        => [LookupFixtures::STYLE_SMART_CASUAL, LookupFixtures::STYLE_CASUAL],
                'seasons'       => [LookupFixtures::SEASON_AUTUMN, LookupFixtures::SEASON_WINTER],
            ],
            self::BLUE_STRIPE_SHIRT => [
                'name'             => 'Blaues Streifenhemd',
                'subcategory'      => SubCategoryFixtures::BUTTON_DOWN,
                'min_layer'        => 2,
                'max_layer'        => 2,
                'primary_color'    => LookupFixtures::COLOR_DARK_BLUE,
                'secondary_colors' => [LookupFixtures::COLOR_WHITE],
                'materials'        => [LookupFixtures::MATERIAL_COTTON => 100.0],
                'patterns'         => [LookupFixtures::PATTERN_VERTICAL_STRIPES],
                'styles'           => [LookupFixtures::STYLE_SMART_CASUAL, LookupFixtures::STYLE_CASUAL],
                'seasons'          => [LookupFixtures::SEASON_SPRING, LookupFixtures::SEASON_SUMMER],
            ],

            // --- Lower body ---
            self::ANTHRACITE_PANTS => [
                'name'          => 'Anthrazit Anzughose',
                'subcategory'   => SubCategoryFixtures::DRESS_TROUSERS,
                'min_layer'     => 1,
                'max_layer'     => 1,
                'primary_color' => LookupFixtures::COLOR_ANTHRACITE,
                'materials'     => [LookupFixtures::MATERIAL_WOOL => 100.0],
                'patterns'      => [LookupFixtures::PATTERN_SOLID],
                'styles'        => [LookupFixtures::STYLE_BUSINESS, LookupFixtures::STYLE_SMART_CASUAL],
                'seasons'       => [
                    LookupFixtures::SEASON_SPRING,
                    LookupFixtures::SEASON_AUTUMN,
                    LookupFixtures::SEASON_WINTER
                ],
            ],
            self::COGNAC_CHINO => [
                'name'          => 'Cognacbraune Chinohose',
                'subcategory'   => SubCategoryFixtures::CHINO,
                'min_layer'     => 1,
                'max_layer'     => 1,
                'primary_color' => LookupFixtures::COLOR_COGNAC,
                'materials'     => [LookupFixtures::MATERIAL_COTTON => 100.0],
                'patterns'      => [LookupFixtures::PATTERN_SOLID],
                'styles'        => [LookupFixtures::STYLE_SMART_CASUAL, LookupFixtures::STYLE_CASUAL],
                'seasons'       => [LookupFixtures::SEASON_SPRING, LookupFixtures::SEASON_AUTUMN],
            ],
            self::DARK_BLUE_JEANS => [
                'name'          => 'Dunkelblaue Jeans',
                'subcategory'   => SubCategoryFixtures::JEANS,
                'min_layer'     => 1,
                'max_layer'     => 1,
                'primary_color' => LookupFixtures::COLOR_DARK_BLUE,
                'materials'     => [LookupFixtures::MATERIAL_DENIM => 100.0],
                'patterns'      => [LookupFixtures::PATTERN_SOLID],
                'styles'        => [LookupFixtures::STYLE_CASUAL, LookupFixtures::STYLE_SMART_CASUAL],
                'seasons'       => [], // no Saison = universal
            ],

            // --- Outer layer ---
            self::GRAY_COAT => [
                'name'          => 'Grauer Wollmantel',
                'subcategory'   => SubCategoryFixtures::COAT,
                'min_layer'     => 4,
                'max_layer'     => 5,
                'primary_color' => LookupFixtures::COLOR_MEDIUM_GRAY,
                'materials'     => [LookupFixtures::MATERIAL_WOOL => 100.0],
                'patterns'      => [LookupFixtures::PATTERN_SOLID],
                'styles'        => [LookupFixtures::STYLE_BUSINESS, LookupFixtures::STYLE_SMART_CASUAL],
                'seasons'       => [LookupFixtures::SEASON_AUTUMN, LookupFixtures::SEASON_WINTER],
            ],
            self::BEIGE_TRENCH => [
                'name'               => 'Beiger Trenchcoat',
                'subcategory'        => SubCategoryFixtures::TRENCH_COAT,
                'min_layer'          => 4,
                'max_layer'          => 5,
                'primary_color'      => LookupFixtures::COLOR_BEIGE,
                'materials'          => [
                    LookupFixtures::MATERIAL_COTTON    => 65.0,
                    LookupFixtures::MATERIAL_POLYESTER => 35.0,
                ],
                'patterns'           => [LookupFixtures::PATTERN_SOLID],
                'styles'             => [LookupFixtures::STYLE_SMART_CASUAL, LookupFixtures::STYLE_BUSINESS],
                'seasons'            => [LookupFixtures::SEASON_SPRING, LookupFixtures::SEASON_AUTUMN],
                'weather_conditions' => [LookupFixtures::WEATHER_RAINY, LookupFixtures::WEATHER_WINDY],
            ],

            // --- Shoes ---
            self::COGNAC_SHOES => [
                'name'          => 'Cognacfarbene Lederschuhe',
                'subcategory'   => SubCategoryFixtures::LEATHER_SHOE,
                'min_layer'     => 1,
                'max_layer'     => 1,
                'primary_color' => LookupFixtures::COLOR_COGNAC,
                'materials'     => [LookupFixtures::MATERIAL_LEATHER => 100.0],
                'patterns'      => [LookupFixtures::PATTERN_SOLID],
                'styles'        => [LookupFixtures::STYLE_SMART_CASUAL, LookupFixtures::STYLE_BUSINESS],
                'seasons'       => [],
            ],
            self::WHITE_SNEAKERS => [
                'name'          => 'Weiße Sneaker',
                'subcategory'   => SubCategoryFixtures::SNEAKER,
                'min_layer'     => 1,
                'max_layer'     => 1,
                'primary_color' => LookupFixtures::COLOR_WHITE,
                'materials'     => [LookupFixtures::MATERIAL_COTTON => 100.0],
                'patterns'      => [LookupFixtures::PATTERN_SOLID],
                'styles'        => [LookupFixtures::STYLE_CASUAL],
                'seasons'       => [],
            ],

            // --- Accessory ---
            self::GRAY_SCARF => [
                'name'               => 'Grauer Wollschal',
                'subcategory'        => SubCategoryFixtures::SCARF,
                'min_layer'          => 1,
                'max_layer'          => 1,
                'primary_color'      => LookupFixtures::COLOR_LIGHT_GRAY,
                'materials'          => [LookupFixtures::MATERIAL_WOOL => 100.0],
                'patterns'           => [LookupFixtures::PATTERN_SOLID],
                'styles'             => [LookupFixtures::STYLE_CASUAL, LookupFixtures::STYLE_SMART_CASUAL],
                'seasons'            => [LookupFixtures::SEASON_AUTUMN, LookupFixtures::SEASON_WINTER],
                'weather_conditions' => [LookupFixtures::WEATHER_COLD, LookupFixtures::WEATHER_WINDY],
            ],
            self::BROWN_BELT => [
                'name'          => 'Brauner Ledergürtel',
                'subcategory'   => SubCategoryFixtures::BELT,
                'min_layer'     => 1,
                'max_layer'     => 1,
                'primary_color' => LookupFixtures::COLOR_COGNAC,
                'materials'     => [LookupFixtures::MATERIAL_LEATHER => 100.0],
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
            SubCategoryFixtures::class,
            LookupFixtures::class,
        ];
    }
}
