<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ClothingItem;
use App\Entity\ItemColor;

readonly class ColorReductionService
{
    public function normalize(ClothingItem $item): void
    {
        $pattern = $item->getPattern();
        $maxSecondary = $pattern->getMaxSecondaryColor();

        $secondaryColors = array_filter(
            $item->getItemColors()->toArray(),
            fn(ItemColor $color) => !$color->isPrimary()
        );

        foreach (array_slice($secondaryColors, $maxSecondary) as $color) {
            $item->removeItemColor($color);
        }
    }
}
