<?php

declare(strict_types=1);

namespace App\Color\Matcher;

use App\Entity\Color;
use DomainException;

readonly class ColorMatchingService
{
    public function __construct(
        private ColorDistanceService $colorDistanceService
    ) {
    }

    /**
     * Find the next closest color from the list to the given HexCode.
     *
     * @param string $hexCode
     * @param list<Color> $colors
     * @return Color
     */
    public function findClosest(string $hexCode, array $colors): Color
    {
        if ($colors === []) {
            throw new DomainException('Color list must not be empty.');
        }

        $closest      = $colors[0];
        $minDistance  = PHP_FLOAT_MAX;

        foreach ($colors as $color) {
            $distance = $this->colorDistanceService->deltaE(
                $hexCode,
                $color->getHexCode()
            );

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $closest     = $color;
            }
        }

        return $closest;
    }
}
