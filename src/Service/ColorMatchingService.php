<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Color;

class ColorMatchingService
{
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
            $distance = $this->calculateDistance($hexCode, $color->getHexCode());

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

        if ($closest === null) {
            return null;
        }

        $distance = $this->calculateDistance($hexCode, $closest->getHexCode());

        return $distance <= $threshold ? $closest : null;
    }

    /**
     * Calculates the Delta-E color difference between two HexCodes.
     * Uses the CIE76 formula for color difference, which is enough for clothing colors.
     */
    public function calculateDistance(string $hexA, string $hexB): float
    {
        $labA = $this->hexToLab($hexA);
        $labB = $this->hexToLab($hexB);

        return sqrt(
            ($labA['L'] - $labB['L']) ** 2 +
            ($labA['a'] - $labB['a']) ** 2 +
            ($labA['b'] - $labB['b']) ** 2
        );
    }

    /**
     * Converts HexCode → RGB → XYZ → LAB
     * @return array<string, float>
     */
    private function hexToLab(string $hex): array
    {
        $rgb = $this->hexToRgb($hex);
        $xyz = $this->rgbToXyz($rgb['r'], $rgb['g'], $rgb['b']);
        return $this->xyzToLab($xyz['x'], $xyz['y'], $xyz['z']);
    }

    /**
     * HexCode → RGB (0-255)
     * @return array<string, int>
     */
    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        return [
            'r' => (int) hexdec(substr($hex, 0, 2)),
            'g' => (int) hexdec(substr($hex, 2, 2)),
            'b' => (int) hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * RGB → XYZ (D65 Illuminant)
     * @return array<string, float>
     */
    private function rgbToXyz(int $r, int $g, int $b): array
    {
        // Linearize
        $r = $r / 255;
        $g = $g / 255;
        $b = $b / 255;

        $r = $r > 0.04045 ? (($r + 0.055) / 1.055) ** 2.4 : $r / 12.92;
        $g = $g > 0.04045 ? (($g + 0.055) / 1.055) ** 2.4 : $g / 12.92;
        $b = $b > 0.04045 ? (($b + 0.055) / 1.055) ** 2.4 : $b / 12.92;

        $r *= 100;
        $g *= 100;
        $b *= 100;

        return [
            'x' => $r * 0.4124 + $g * 0.3576 + $b * 0.1805,
            'y' => $r * 0.2126 + $g * 0.7152 + $b * 0.0722,
            'z' => $r * 0.0193 + $g * 0.1192 + $b * 0.9505,
        ];
    }

    /**
     * XYZ → LAB (D65 Reference Values)
     * @return array<string, float>
     */
    private function xyzToLab(float $x, float $y, float $z): array
    {
        // D65 Reference Values
        $x /= 95.047;
        $y /= 100.000;
        $z /= 108.883;

        $x = $x > 0.008856 ? $x ** (1 / 3) : (7.787 * $x) + (16 / 116);
        $y = $y > 0.008856 ? $y ** (1 / 3) : (7.787 * $y) + (16 / 116);
        $z = $z > 0.008856 ? $z ** (1 / 3) : (7.787 * $z) + (16 / 116);

        return [
            'L' => (116 * $y) - 16,
            'a' => 500 * ($x - $y),
            'b' => 200 * ($y - $z),
        ];
    }
}
