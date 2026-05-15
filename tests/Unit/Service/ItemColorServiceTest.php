<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\ClothingItem;
use App\Entity\Color;
use App\Entity\ItemColor;
use App\Service\ItemColorService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ItemColorServiceTest extends TestCase
{
    private ItemColorService $service;

    protected function setUp(): void
    {
        $this->service = new ItemColorService();
    }

    // --- Helper methods ---

    private function makeColor(int $id): Color
    {
        $color = new Color();
        $ref   = new ReflectionClass($color);
        $prop  = $ref->getProperty('id');
        $prop->setValue($color, $id);
        return $color;
    }

    /**
     * @param array<int> $colorIds
     * @return ClothingItem
     */
    private function makeItemWithColors(array $colorIds): ClothingItem
    {
        $item = new ClothingItem();

        /** @var int $id */
        foreach ($colorIds as $id) {
            $color     = $this->makeColor($id);
            $itemColor = new ItemColor($item, $color, $id === 1);
            $item->addItemColor($itemColor);
        }

        return $item;
    }

    // -------------------------------------------------------
    // Update primary color
    // -------------------------------------------------------

    public function testUpdatePrimaryColorSetsPrimaryFlag(): void
    {
        $item     = $this->makeItemWithColors([1, 2, 3]);
        $newColor = $this->makeColor(2);

        $this->service->updatePrimaryColor($item, $newColor);

        foreach ($item->getItemColors() as $itemColor) {
            $expected = $itemColor->getColor()->getId() === 2;
            $this->assertEquals($expected, $itemColor->isPrimary());
        }
    }

    public function testUpdatePrimaryColorWithNullDoesNothing(): void
    {
        $item = $this->makeItemWithColors([1, 2]);

        $this->service->updatePrimaryColor($item, null);

        // Primary color is not changed
        $primaryColors = array_filter(
            $item->getItemColors()->toArray(),
            fn(ItemColor $ic) => $ic->isPrimary()
        );

        $this->assertCount(1, $primaryColors);
        $this->assertEquals(1, array_values($primaryColors)[0]->getColor()->getId());
    }

    public function testUpdatePrimaryColorWithEmptyColorsDoesNothing(): void
    {
        $item     = new ClothingItem();
        $newColor = $this->makeColor(1);

        // No error with empty item
        $this->service->updatePrimaryColor($item, $newColor);

        $this->assertCount(0, $item->getItemColors());
    }

    public function testOnlyOneColorIsPrimaryAfterUpdate(): void
    {
        $item     = $this->makeItemWithColors([1, 2, 3]);
        $newColor = $this->makeColor(3);

        $this->service->updatePrimaryColor($item, $newColor);

        $primaryColors = array_filter(
            $item->getItemColors()->toArray(),
            fn(ItemColor $ic) => $ic->isPrimary()
        );

        $this->assertCount(1, $primaryColors);
        $this->assertEquals(3, array_values($primaryColors)[0]->getColor()->getId());
    }
}
