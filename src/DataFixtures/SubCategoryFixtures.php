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

    // Lower Body
    public const string JEANS           = 'subcategory-jeans';
    public const string CHINO           = 'subcategory-chino';
    public const string DRESS_TROUSERS  = 'subcategory-dress-trousers';

    // Full Body
    public const string SUIT            = 'subcategory-suit';
    public const string JUMPSUIT        = 'subcategory-jumpsuit';

    // Outer Layer
    public const string COAT            = 'subcategory-coat';
    public const string JACKET          = 'subcategory-jacket';
    public const string TRENCH_COAT     = 'subcategory-trench-coat';
    public const string BLAZER          = 'subcategory-blazer';

    // Shoes
    public const string SNEAKER         = 'subcategory-sneaker';
    public const string LOAFER          = 'subcategory-loafer';
    public const string LEATHER_SHOE    = 'subcategory-leather-shoe';
    public const string BOOT            = 'subcategory-boot';

    // Head
    public const string CAP             = 'subcategory-cap';
    public const string BEANIE          = 'subcategory-beanie';
    public const string HAT             = 'subcategory-hat';

    // Accessory
    public const string SCARF           = 'subcategory-scarf';
    public const string BELT            = 'subcategory-belt';
    public const string GLOVES          = 'subcategory-gloves';

    public function load(ObjectManager $manager): void
    {
        $subcategories = [
            // Upper Body
            self::T_SHIRT        => ['name' => 'T-Shirt',       'category' => CategoryFixtures::UPPER_BODY],
            self::POLO_SHIRT     => ['name' => 'Polo-Shirt',    'category' => CategoryFixtures::UPPER_BODY],
            self::BUTTON_DOWN    => ['name' => 'Hemd',          'category' => CategoryFixtures::UPPER_BODY],
            self::HOODIE         => ['name' => 'Hoodie',        'category' => CategoryFixtures::UPPER_BODY],
            self::PULLOVER       => ['name' => 'Pullover',      'category' => CategoryFixtures::UPPER_BODY],
            self::VEST           => ['name' => 'Weste',         'category' => CategoryFixtures::UPPER_BODY],

            // Lower Body
            self::JEANS          => ['name' => 'Jeans',         'category' => CategoryFixtures::LOWER_BODY],
            self::CHINO          => ['name' => 'Chino',         'category' => CategoryFixtures::LOWER_BODY],
            self::DRESS_TROUSERS => ['name' => 'Anzughose',     'category' => CategoryFixtures::LOWER_BODY],

            // Full Body
            self::SUIT           => ['name' => 'Anzug',         'category' => CategoryFixtures::FULL_BODY],
            self::JUMPSUIT       => ['name' => 'Jumpsuit',      'category' => CategoryFixtures::FULL_BODY],

            // Outer Layer
            self::COAT           => ['name' => 'Mantel',        'category' => CategoryFixtures::OUTER_LAYER],
            self::JACKET         => ['name' => 'Jacke',         'category' => CategoryFixtures::OUTER_LAYER],
            self::TRENCH_COAT    => ['name' => 'Trenchcoat',    'category' => CategoryFixtures::OUTER_LAYER],
            self::BLAZER         => ['name' => 'Blazer',        'category' => CategoryFixtures::OUTER_LAYER],

            // Shoes
            self::SNEAKER        => ['name' => 'Sneaker',       'category' => CategoryFixtures::FOOTWEAR],
            self::LOAFER         => ['name' => 'Loafer',        'category' => CategoryFixtures::FOOTWEAR],
            self::LEATHER_SHOE   => ['name' => 'Lederschuh',    'category' => CategoryFixtures::FOOTWEAR],
            self::BOOT           => ['name' => 'Stiefel',       'category' => CategoryFixtures::FOOTWEAR],

            // Head
            self::CAP            => ['name' => 'Cap',           'category' => CategoryFixtures::HEAD],
            self::BEANIE         => ['name' => 'Beanie',        'category' => CategoryFixtures::HEAD],
            self::HAT            => ['name' => 'Hut',           'category' => CategoryFixtures::HEAD],

            // Accessory
            self::SCARF          => ['name' => 'Schal',         'category' => CategoryFixtures::ACCESSORY],
            self::BELT           => ['name' => 'Gürtel',        'category' => CategoryFixtures::ACCESSORY],
            self::GLOVES         => ['name' => 'Handschuhe',    'category' => CategoryFixtures::ACCESSORY],
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
