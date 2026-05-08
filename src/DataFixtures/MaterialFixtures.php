<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Material;
use App\Enum\MaterialCategory;
use App\Enum\WarmthLevel;
use App\Enum\BreathabilityLevel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MaterialFixtures extends Fixture
{
    public const string MATERIAL_COTTON    = 'material-cotton';
    public const string MATERIAL_WOOL      = 'material-wool';
    public const string MATERIAL_CASHMERE  = 'material-cashmere';
    public const string MATERIAL_LEATHER   = 'material-leather';
    public const string MATERIAL_LINEN     = 'material-linen';
    public const string MATERIAL_DENIM     = 'material-denim';
    public const string MATERIAL_POLYESTER = 'material-polyester';
    public const string MATERIAL_SILK      = 'material-silk';
    public const string MATERIAL_NYLON     = 'material-nylon';
    public const string MATERIAL_ELASTANE  = 'material-elastane';
    public const string MATERIAL_VISCOSE   = 'material-viscose';
    public const string MATERIAL_FLEECE    = 'material-fleece';
    public const string MATERIAL_SUEDE     = 'material-suede';
    public const string MATERIAL_CORDUROY  = 'material-corduroy';
    public const string MATERIAL_VELVET    = 'material-velvet';
    public const string MATERIAL_DOWN      = 'material-down';
    public const string MATERIAL_GORE_TEX  = 'material-gore-tex';
    public const string MATERIAL_SOFTSHELL = 'material-softshell';

    public function load(ObjectManager $manager): void
    {
        $materials = [
            // 🧶 NATURAL
            self::MATERIAL_COTTON => [
                'name' => 'cotton',
                'category' => MaterialCategory::NATURAL,
                'warmth' => WarmthLevel::LOW,
                'breathability' => BreathabilityLevel::HIGH,
                'waterproof' => false,
                'stretch' => false,
                'windproof' => false,
            ],

            self::MATERIAL_WOOL => [
                'name' => 'wool',
                'category' => MaterialCategory::NATURAL,
                'warmth' => WarmthLevel::HIGH,
                'breathability' => BreathabilityLevel::MEDIUM,
                'waterproof' => false,
                'stretch' => false,
                'windproof' => true,
            ],

            self::MATERIAL_LINEN => [
                'name' => 'linen',
                'category' => MaterialCategory::NATURAL,
                'warmth' => WarmthLevel::LOW,
                'breathability' => BreathabilityLevel::HIGH,
                'waterproof' => false,
                'stretch' => false,
                'windproof' => false,
            ],

            self::MATERIAL_SILK => [
                'name' => 'silk',
                'category' => MaterialCategory::NATURAL,
                'warmth' => WarmthLevel::LOW,
                'breathability' => BreathabilityLevel::HIGH,
                'waterproof' => false,
                'stretch' => false,
                'windproof' => false,
            ],

            self::MATERIAL_CASHMERE => [
                'name' => 'cashmere',
                'category' => MaterialCategory::NATURAL,
                'warmth' => WarmthLevel::HIGH,
                'breathability' => BreathabilityLevel::MEDIUM,
                'waterproof' => false,
                'stretch' => false,
                'windproof' => true,
            ],

            // 🧪 SYNTHETIC
            self::MATERIAL_POLYESTER => [
                'name' => 'polyester',
                'category' => MaterialCategory::SYNTHETIC,
                'warmth' => WarmthLevel::MEDIUM,
                'breathability' => BreathabilityLevel::LOW,
                'waterproof' => false,
                'stretch' => false,
                'windproof' => true,
            ],

            self::MATERIAL_NYLON => [
                'name' => 'nylon',
                'category' => MaterialCategory::SYNTHETIC,
                'warmth' => WarmthLevel::LOW,
                'breathability' => BreathabilityLevel::LOW,
                'waterproof' => true,
                'stretch' => false,
                'windproof' => true,
            ],

            self::MATERIAL_ELASTANE => [
                'name' => 'elastane',
                'category' => MaterialCategory::SYNTHETIC,
                'warmth' => WarmthLevel::LOW,
                'breathability' => BreathabilityLevel::MEDIUM,
                'waterproof' => false,
                'stretch' => true,
                'windproof' => false,
            ],

            self::MATERIAL_VISCOSE => [
                'name' => 'viscose',
                'category' => MaterialCategory::SYNTHETIC,
                'warmth' => WarmthLevel::LOW,
                'breathability' => BreathabilityLevel::HIGH,
                'waterproof' => false,
                'stretch' => false,
                'windproof' => false,
            ],

            // 🧥 TEXTURE
            self::MATERIAL_DENIM => [
                'name' => 'denim',
                'category' => MaterialCategory::TEXTURE,
                'warmth' => WarmthLevel::MEDIUM,
                'breathability' => BreathabilityLevel::MEDIUM,
                'waterproof' => false,
                'stretch' => false,
                'windproof' => true,
            ],

            self::MATERIAL_LEATHER => [
                'name' => 'leather',
                'category' => MaterialCategory::TEXTURE,
                'warmth' => WarmthLevel::HIGH,
                'breathability' => BreathabilityLevel::LOW,
                'waterproof' => true,
                'stretch' => false,
                'windproof' => true,
            ],

            self::MATERIAL_SUEDE => [
                'name' => 'suede',
                'category' => MaterialCategory::TEXTURE,
                'warmth' => WarmthLevel::MEDIUM,
                'breathability' => BreathabilityLevel::LOW,
                'waterproof' => false,
                'stretch' => false,
                'windproof' => true,
            ],

            self::MATERIAL_CORDUROY => [
                'name' => 'corduroy',
                'category' => MaterialCategory::TEXTURE,
                'warmth' => WarmthLevel::MEDIUM,
                'breathability' => BreathabilityLevel::LOW,
                'waterproof' => false,
                'stretch' => false,
                'windproof' => true,
            ],

            self::MATERIAL_VELVET => [
                'name' => 'velvet',
                'category' => MaterialCategory::TEXTURE,
                'warmth' => WarmthLevel::MEDIUM,
                'breathability' => BreathabilityLevel::LOW,
                'waterproof' => false,
                'stretch' => false,
                'windproof' => true,
            ],

            self::MATERIAL_FLEECE => [
                'name' => 'fleece',
                'category' => MaterialCategory::TEXTURE,
                'warmth' => WarmthLevel::HIGH,
                'breathability' => BreathabilityLevel::MEDIUM,
                'waterproof' => false,
                'stretch' => true,
                'windproof' => false,
            ],

            // 🌧 FUNCTIONAL
            self::MATERIAL_DOWN => [
                'name' => 'down',
                'category' => MaterialCategory::FUNCTIONAL,
                'warmth' => WarmthLevel::HIGH,
                'breathability' => BreathabilityLevel::MEDIUM,
                'waterproof' => false,
                'stretch' => false,
                'windproof' => true,
            ],

            self::MATERIAL_GORE_TEX => [
                'name' => 'gore_tex',
                'category' => MaterialCategory::FUNCTIONAL,
                'warmth' => WarmthLevel::MEDIUM,
                'breathability' => BreathabilityLevel::MEDIUM,
                'waterproof' => true,
                'stretch' => false,
                'windproof' => true,
            ],

            self::MATERIAL_SOFTSHELL => [
                'name' => 'softshell',
                'category' => MaterialCategory::FUNCTIONAL,
                'warmth' => WarmthLevel::MEDIUM,
                'breathability' => BreathabilityLevel::HIGH,
                'waterproof' => true,
                'stretch' => true,
                'windproof' => true,
            ],
        ];

        foreach ($materials as $reference => $data) {
            $material = new Material();
            $material->setName($data['name']);
            $material->setCategory($data['category']);
            $material->setWarmth($data['warmth']);
            $material->setBreathability($data['breathability']);
            $material->setWaterproof($data['waterproof']);
            $material->setStretch($data['stretch']);
            $material->setWindproof($data['windproof']);
            $manager->persist($material);
            $this->addReference($reference, $material);
        }

        $manager->flush();
    }
}
