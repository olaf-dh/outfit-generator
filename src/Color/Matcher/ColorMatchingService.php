<?php

declare(strict_types=1);

namespace App\Color\Matcher;

use App\DTO\Color\ExtractedColor;
use App\Entity\Color;
use App\Repository\ColorRepository;

readonly class ColorMatchingService
{
    public function __construct(
        private ColorRepository $colorRepository,
        private ColorDistanceService $colorDistanceService
    ) {
    }

    /**
     * Find the next closest color from the list to the given HexCode.
     *
     * @param Color[] $colors
     */
    public function findClosest(string $hexCode, array $colors): ?Color
    {
        if (empty($colors)) {
            return null;
        }

        $closest      = null;
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

    /**
     * Find the next closest color within a given Delta-E threshold.
     * Returns null if no color is within the threshold.
     *
     * @param Color[] $colors
     */
    public function findClosestWithinThreshold(
        string $hexCode,
        array $colors,
        float $threshold
    ): ?Color {
        $closest = $this->findClosest($hexCode, $colors);
        if (!$closest) {
            return null;
        }

        $distance = $this->colorDistanceService->deltaE($hexCode, $closest->getHexCode());

        if ($distance > $threshold) {
            return null;
        }

        return $closest;
    }

    /**
     * @param ExtractedColor $input
     * @return Color|null
     */
    public function match(ExtractedColor $input): ?Color
    {
        $colors = $this->colorRepository->findAll();

        $bestDistance = PHP_FLOAT_MAX;
        $bestColor = null;

        foreach ($colors as $color) {
            $distance = $this->calculateDistance($input, $color);

            if ($distance < $bestDistance) {
                $bestDistance = $distance;
                $bestColor = $color;
            }
        }

        return $bestColor;
    }

    public function calculateDistance(ExtractedColor $input, Color $color): float
    {
        $hueDistance = min(
            abs($input->h - $color->getH()),
            360 - abs($input->h - $color->getH())
        ) / 180;

        $saturationDistance = abs(
            $input->s - $color->getS()
        );

        $valueDistance = abs(
            $input->v - $color->getV()
        );

        return (
            ($hueDistance * 0.65)
            + ($saturationDistance * 0.20)
            + ($valueDistance * 0.15)
        );
    }
}
