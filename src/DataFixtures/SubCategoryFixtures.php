<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\SubCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SubCategoryFixtures extends Fixture implements DependentFixtureInterface
{
    // Upper Body
    public const string T_SHIRT         = 'subcategory-t-shirt';
    public const string POLO_SHIRT      = 'subcategory-polo-shirt';
    public const string BUTTON_DOWN     = 'subcategory-button-down';
    public const string HOODIE          = 'subcategory-hoodie';
    public const string PULLOVER        = 'subcategory-pullover';
    public const string VEST            = 'subcategory-vest';
    public const string LONG_SLEEVE     = 'subcategory-long-sleeve';
    public const string TANK_TOP        = 'subcategory-tank-top';
    public const string BLOUSE          = 'subcategory-blouse';
    public const string SWEATER         = 'subcategory-sweater';
    public const string CARDIGAN        = 'subcategory-cardigan';
    public const string TURTLENECK      = 'subcategory-turtleneck';
    public const string CROP_TOP        = 'subcategory-crop-top';
    public const string TUNICA          = 'subcategory-tunica';
    public const string KIMONO          = 'subcategory-kimono';
    public const string PONCHO          = 'subcategory-poncho';

    // Lower Body
    public const string JEANS           = 'subcategory-jeans';
    public const string CHINO           = 'subcategory-chino';
    public const string TROUSERS        = 'subcategory-trousers';
    public const string SHORTS          = 'subcategory-shorts';
    public const string SWEAT_PANTS     = 'subcategory-sweat-pants';
    public const string CARGO_PANTS     = 'subcategory-cargo-pants';
    public const string LEGGINGS        = 'subcategory-leggings';
    public const string SKIRT           = 'subcategory-skirt';
    public const string MAXI_SKIRT      = 'subcategory-maxi-skirt';

    // Full Body
    public const string SUIT            = 'subcategory-suit';
    public const string JUMPSUIT        = 'subcategory-jumpsuit';
    public const string DRESS           = 'subcategory-dress';
    public const string PYJAMA          = 'subcategory-pyjama';
    public const string SPORTSWEAR      = 'subcategory-sportswear';

    // Outer Layer
    public const string COAT            = 'subcategory-coat';
    public const string JACKET          = 'subcategory-jacket';
    public const string TRENCH_COAT     = 'subcategory-trench-coat';
    public const string BLAZER          = 'subcategory-blazer';
    public const string PARKA           = 'subcategory-parka';

    // Shoes
    public const string SNEAKER         = 'subcategory-sneaker';
    public const string LOAFER          = 'subcategory-loafer';
    public const string BUSINESS_SHOES  = 'subcategory-business-shoes';
    public const string BOOTS           = 'subcategory-boots';
    public const string SANDALS         = 'subcategory-sandals';
    public const string HIGH_HEELS      = 'subcategory-high-heels';
    public const string SPORT_SHOES     = 'subcategory-sport-shoes';
    public const string CHELSEA_BOOTS   = 'subcategory-chelsea-boots';
    public const string MOCCASINS       = 'subcategory-moccasins';
    public const string ESPADRILLES     = 'subcategory-espadrilles';

    // Head
    public const string CAP             = 'subcategory-cap';
    public const string BEANIE          = 'subcategory-beanie';
    public const string HAT             = 'subcategory-hat';

    // Accessory
    public const string SCARF           = 'subcategory-scarf';
    public const string BELT            = 'subcategory-belt';
    public const string GLOVES          = 'subcategory-gloves';
    public const string BAG             = 'subcategory-bag';
    public const string SHOULDER_BAG    = 'subcategory-shoulder-bag';
    public const string BACKPACK        = 'subcategory-backpack';
    public const string NECKTIE         = 'subcategory-necktie';
    public const string SUN_GLASSES     = 'subcategory-sun-glasses';

    public function load(ObjectManager $manager): void
    {
        $subcategories = [
            // Upper Body
            self::T_SHIRT        => ['name' => 't_shirt',       'category' => CategoryFixtures::UPPER_BODY],
            self::POLO_SHIRT     => ['name' => 'polo_shirt',    'category' => CategoryFixtures::UPPER_BODY],
            self::BUTTON_DOWN    => ['name' => 'button_down',   'category' => CategoryFixtures::UPPER_BODY],
            self::HOODIE         => ['name' => 'hoodie',        'category' => CategoryFixtures::UPPER_BODY],
            self::PULLOVER       => ['name' => 'pullover',      'category' => CategoryFixtures::UPPER_BODY],
            self::VEST           => ['name' => 'vest',          'category' => CategoryFixtures::UPPER_BODY],
            self::LONG_SLEEVE    => ['name' => 'long_sleeve',   'category' => CategoryFixtures::UPPER_BODY],
            self::TANK_TOP       => ['name' => 'tank_top',      'category' => CategoryFixtures::UPPER_BODY],
            self::BLOUSE         => ['name' => 'blouse',        'category' => CategoryFixtures::UPPER_BODY],
            self::SWEATER        => ['name' => 'sweater',       'category' => CategoryFixtures::UPPER_BODY],
            self::CARDIGAN       => ['name' => 'cardigan',      'category' => CategoryFixtures::UPPER_BODY],
            self::TURTLENECK     => ['name' => 'turtleneck',    'category' => CategoryFixtures::UPPER_BODY],
            self::CROP_TOP       => ['name' => 'crop_top',      'category' => CategoryFixtures::UPPER_BODY],
            self::TUNICA         => ['name' => 'tunica',        'category' => CategoryFixtures::UPPER_BODY],
            self::KIMONO         => ['name' => 'kimono',        'category' => CategoryFixtures::UPPER_BODY],
            self::PONCHO         => ['name' => 'poncho',        'category' => CategoryFixtures::UPPER_BODY],

            // Lower Body
            self::JEANS          => ['name' => 'jeans',         'category' => CategoryFixtures::LOWER_BODY],
            self::CHINO          => ['name' => 'chino',         'category' => CategoryFixtures::LOWER_BODY],
            self::TROUSERS       => ['name' => 'trousers',      'category' => CategoryFixtures::LOWER_BODY],
            self::SHORTS         => ['name' => 'shorts',        'category' => CategoryFixtures::LOWER_BODY],
            self::SWEAT_PANTS    => ['name' => 'sweat_pants',   'category' => CategoryFixtures::LOWER_BODY],
            self::CARGO_PANTS    => ['name' => 'cargo_pants',   'category' => CategoryFixtures::LOWER_BODY],
            self::LEGGINGS       => ['name' => 'trousers',      'category' => CategoryFixtures::LOWER_BODY],
            self::SKIRT          => ['name' => 'skirt',         'category' => CategoryFixtures::LOWER_BODY],
            self::MAXI_SKIRT     => ['name' => 'maxi_skirt',    'category' => CategoryFixtures::LOWER_BODY],

            // Full Body
            self::SUIT           => ['name' => 'suit',          'category' => CategoryFixtures::FULL_BODY],
            self::JUMPSUIT       => ['name' => 'jumpsuit',      'category' => CategoryFixtures::FULL_BODY],
            self::DRESS          => ['name' => 'dress',         'category' => CategoryFixtures::FULL_BODY],
            self::SPORTSWEAR     => ['name' => 'sportswear',    'category' => CategoryFixtures::FULL_BODY],
            self::PYJAMA         => ['name' => 'pyjama',        'category' => CategoryFixtures::FULL_BODY],

            // Outer Layer
            self::COAT           => ['name' => 'coat',          'category' => CategoryFixtures::OUTER_LAYER],
            self::JACKET         => ['name' => 'jacket',        'category' => CategoryFixtures::OUTER_LAYER],
            self::TRENCH_COAT    => ['name' => 'trenchcoat',    'category' => CategoryFixtures::OUTER_LAYER],
            self::BLAZER         => ['name' => 'blazer',        'category' => CategoryFixtures::OUTER_LAYER],
            self::PARKA          => ['name' => 'parka',         'category' => CategoryFixtures::OUTER_LAYER],

            // Shoes
            self::SNEAKER        => ['name' => 'sneaker',       'category' => CategoryFixtures::FOOTWEAR],
            self::LOAFER         => ['name' => 'loafer',        'category' => CategoryFixtures::FOOTWEAR],
            self::BUSINESS_SHOES => ['name' => 'business_shoes','category' => CategoryFixtures::FOOTWEAR],
            self::BOOTS          => ['name' => 'boots',         'category' => CategoryFixtures::FOOTWEAR],
            self::SANDALS        => ['name' => 'sandals',       'category' => CategoryFixtures::FOOTWEAR],
            self::HIGH_HEELS     => ['name' => 'high_heels',    'category' => CategoryFixtures::FOOTWEAR],
            self::SPORT_SHOES    => ['name' => 'sport_shoes',   'category' => CategoryFixtures::FOOTWEAR],
            self::CHELSEA_BOOTS  => ['name' => 'chelsea_boots', 'category' => CategoryFixtures::FOOTWEAR],
            self::MOCCASINS      => ['name' => 'moccasins',     'category' => CategoryFixtures::FOOTWEAR],
            self::ESPADRILLES    => ['name' => 'espadrilles',   'category' => CategoryFixtures::FOOTWEAR],

            // Head
            self::CAP            => ['name' => 'cap',           'category' => CategoryFixtures::HEAD],
            self::BEANIE         => ['name' => 'beanie',        'category' => CategoryFixtures::HEAD],
            self::HAT            => ['name' => 'hat',           'category' => CategoryFixtures::HEAD],

            // Accessory
            self::SCARF          => ['name' => 'scarf',         'category' => CategoryFixtures::ACCESSORY],
            self::BELT           => ['name' => 'belt',          'category' => CategoryFixtures::ACCESSORY],
            self::BAG            => ['name' => 'bag',           'category' => CategoryFixtures::ACCESSORY],
            self::SHOULDER_BAG   => ['name' => 'shoulder_bag',  'category' => CategoryFixtures::ACCESSORY],
            self::BACKPACK       => ['name' => 'backpack',      'category' => CategoryFixtures::ACCESSORY],
            self::NECKTIE        => ['name' => 'necktie',       'category' => CategoryFixtures::ACCESSORY],
            self::SUN_GLASSES    => ['name' => 'sun_glasses',   'category' => CategoryFixtures::ACCESSORY],
            self::GLOVES         => ['name' => 'gloves',        'category' => CategoryFixtures::ACCESSORY],
        ];

        foreach ($subcategories as $reference => $data) {
            $subcategory = new SubCategory();
            $subcategory->setName($data['name']);
            $subcategory->setCategory($this->getReference($data['category'], Category::class));
            $manager->persist($subcategory);
            $this->addReference($reference, $subcategory);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [CategoryFixtures::class];
    }
}
