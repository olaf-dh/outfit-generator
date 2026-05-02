<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Enum\BodyZone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const string UPPER_BODY  = 'category-upper-body';
    public const string LOWER_BODY  = 'category-lower-body';
    public const string FULL_BODY   = 'category-full-body';
    public const string OUTER_LAYER = 'category-outer-layer';
    public const string FOOTWEAR    = 'category-footwear';
    public const string HEAD        = 'category-head';
    public const string ACCESSORY   = 'category-accessory';

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager): void
    {
        $categories = [
            self::UPPER_BODY  => ['name' => 'Oberkörper',   'zone' => BodyZone::UPPER_BODY],
            self::LOWER_BODY  => ['name' => 'Unterkörper',  'zone' => BodyZone::LOWER_BODY],
            self::FULL_BODY   => ['name' => 'Ganzkörper',   'zone' => BodyZone::FULL_BODY],
            self::OUTER_LAYER => ['name' => 'Außenschicht', 'zone' => BodyZone::OUTER_LAYER],
            self::FOOTWEAR    => ['name' => 'Schuhe',       'zone' => BodyZone::FOOTWEAR],
            self::HEAD        => ['name' => 'Kopf',         'zone' => BodyZone::HEAD],
            self::ACCESSORY   => ['name' => 'Accessoire',   'zone' => BodyZone::ACCESSORY],
        ];

        foreach ($categories as $reference => $data) {
            $category = new Category();
            $category->setName($data['name']);
            $category->setBodyZone($data['zone']);
            $manager->persist($category);
            $this->addReference($reference, $category);
        }

        $manager->flush();
    }
}
