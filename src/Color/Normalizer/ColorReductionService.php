<?php

declare(strict_types=1);

namespace App\Color\Normalizer;

use App\Entity\ClothingItem;
use App\Entity\Color;
use App\Entity\ItemColor;

readonly class ColorReductionService
{
    /** deprecated */
    public function normalize(ClothingItem $item): void
    {
        $pattern = $item->getPattern();
        if (!$pattern) {
            return;
        }
        $maxSecondary = $pattern->getMaxSecondaryColor();

        $secondaryColors = array_filter(
            $item->getItemColors()->toArray(),
            fn(ItemColor $color) => !$color->isPrimary()
        );

        foreach (array_slice($secondaryColors, $maxSecondary) as $color) {
            $item->removeItemColor($color);
        }
    }

    /**
     * @param ClothingItem $item
     * @param Color $primaryColor
     * @param list<Color> $secondaryColors
     * @return void
     */
    public function reduction(ClothingItem $item, Color $primaryColor, array $secondaryColors): void
    {
        // Update primary color
        $item->updatePrimaryColor($primaryColor);

        // Get IDs of colors to keep (primary + selected secondary)
        $colorIdsToKeep = array_map(fn(Color $c) => $c->getId(), $secondaryColors);
        $colorIdsToKeep[] = $primaryColor->getId();

        // Remove colors that are not in the list of colors to keep
        $toRemove = $item->getItemColors()->filter(
            fn(ItemColor $ic) => !in_array($ic->getColor()->getId(), $colorIdsToKeep, true)
        );

        foreach ($toRemove as $itemColor) {
            $item->removeItemColor($itemColor);
        }
    }
}
