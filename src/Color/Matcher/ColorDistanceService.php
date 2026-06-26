<?php

declare(strict_types=1);

namespace App\Color\Matcher;

use App\Color\Service\ColorConverterService;

final readonly class ColorDistanceService
{
    public function __construct(private ColorConverterService $converter)
    {
    }

    public function deltaE(string $hexA, string $hexB): float
    {
        $labA = $this->hexToLab($hexA);
        $labB = $this->hexToLab($hexB);

        return sqrt(
            (($labA['L'] - $labB['L']) ** 2)
            + (($labA['a'] - $labB['a']) ** 2)
            + (($labA['b'] - $labB['b']) ** 2)
        );
    }

    /**
     * Converts HexCode → RGB → XYZ → LAB
     * @return array<string, float>
     */
    private function hexToLab(string $hex): array
    {
        $rgb = $this->converter->hexToRgb($hex);
        $xyz = $this->rgbToXyz($rgb->r, $rgb->g, $rgb->b);
        return $this->xyzToLab($xyz['x'], $xyz['y'], $xyz['z']);
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
