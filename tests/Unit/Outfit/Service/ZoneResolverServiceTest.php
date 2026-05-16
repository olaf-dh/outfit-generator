<?php

declare(strict_types=1);

namespace App\Tests\Unit\Outfit\Service;

use App\ClothingItem\Enum\BodyZone;
use App\Entity\Category;
use App\Entity\ClothingItem;
use App\Entity\SubCategory;
use App\Outfit\Service\ZoneResolverService;
use PHPUnit\Framework\TestCase;

class ZoneResolverServiceTest extends TestCase
{
    private ZoneResolverService $service;

    protected function setUp(): void
    {
        $this->service = new ZoneResolverService();
    }

    // --- Helper method ---

    private function makeItem(BodyZone $zone): ClothingItem
    {
        $category = new Category();
        $category->setBodyZone($zone);

        $subCategory = new SubCategory();
        $subCategory->setCategory($category);

        $item = new ClothingItem();
        $item->setSubCategory($subCategory);
        return $item;
    }

    // -------------------------------------------------------
    // Mandatory zones: upper body, lower body, footwear
    // -------------------------------------------------------

    public function testRequiredZonesWithNoSeedItems(): void
    {
        $missing = $this->service->resolveMissingZones([]);

        $this->assertContains(BodyZone::UPPER_BODY, $missing);
        $this->assertContains(BodyZone::LOWER_BODY, $missing);
        $this->assertContains(BodyZone::FOOTWEAR, $missing);
    }

    public function testPulloverCoversUpperBody(): void
    {
        $pullover = $this->makeItem(BodyZone::UPPER_BODY);

        $missing = $this->service->resolveMissingZones([$pullover]);

        $this->assertNotContains(BodyZone::UPPER_BODY, $missing);
        $this->assertContains(BodyZone::LOWER_BODY, $missing);
        $this->assertContains(BodyZone::FOOTWEAR, $missing);
    }

    public function testShoesAndPulloverLeaveOnlyLowerBody(): void
    {
        $pullover = $this->makeItem(BodyZone::UPPER_BODY);
        $shoes   = $this->makeItem(BodyZone::FOOTWEAR);

        $missing = $this->service->resolveMissingZones([$pullover, $shoes]);

        // Responsibility: only LOWER_BODY missing
        $this->assertContains(BodyZone::LOWER_BODY, $missing);
        $this->assertNotContains(BodyZone::UPPER_BODY, $missing);
        $this->assertNotContains(BodyZone::FOOTWEAR, $missing);
    }

    // -------------------------------------------------------
    // User cases: FULL_BODY replaces UPPER_BODY + LOWER_BODY
    // -------------------------------------------------------

    public function testFullBodyCoversUpperAndLowerBody(): void
    {
        $jumpsuit = $this->makeItem(BodyZone::FULL_BODY);

        $missing = $this->service->resolveMissingZones([$jumpsuit]);

        $this->assertNotContains(BodyZone::UPPER_BODY, $missing);
        $this->assertNotContains(BodyZone::LOWER_BODY, $missing);
        $this->assertContains(BodyZone::FOOTWEAR, $missing);
    }

    // -------------------------------------------------------
    // Optional zones: outer layer, head, accessory
    // are always returned as missing if not covered
    // -------------------------------------------------------

    public function testOptionalZonesAreAlwaysIncluded(): void
    {
        $missing = $this->service->resolveMissingZones([]);

        $this->assertContains(BodyZone::OUTER_LAYER, $missing);
        $this->assertContains(BodyZone::HEAD, $missing);
        $this->assertContains(BodyZone::ACCESSORY, $missing);
    }

    public function testOptionalZoneIsRemovedWhenCovered(): void
    {
        $mantel = $this->makeItem(BodyZone::OUTER_LAYER);

        $missing = $this->service->resolveMissingZones([$mantel]);

        // OUTER_LAYER is covered → should not be missing
        $this->assertNotContains(BodyZone::OUTER_LAYER, $missing);

        // but mandatory zones still missing
        $this->assertContains(BodyZone::UPPER_BODY, $missing);
        $this->assertContains(BodyZone::LOWER_BODY, $missing);
        $this->assertContains(BodyZone::FOOTWEAR, $missing);
    }

    // -------------------------------------------------------
    // Complete outfit → only optional zones are missing
    // -------------------------------------------------------

    public function testCompleteRequiredOutfitReturnsOnlyOptionals(): void
    {
        $pullover = $this->makeItem(BodyZone::UPPER_BODY);
        $pants    = $this->makeItem(BodyZone::LOWER_BODY);
        $shoes    = $this->makeItem(BodyZone::FOOTWEAR);

        $missing = $this->service->resolveMissingZones([$pullover, $pants, $shoes]);

        $this->assertNotContains(BodyZone::UPPER_BODY, $missing);
        $this->assertNotContains(BodyZone::LOWER_BODY, $missing);
        $this->assertNotContains(BodyZone::FOOTWEAR, $missing);

        $this->assertContains(BodyZone::OUTER_LAYER, $missing);
        $this->assertContains(BodyZone::HEAD, $missing);
        $this->assertContains(BodyZone::ACCESSORY, $missing);
    }
}
