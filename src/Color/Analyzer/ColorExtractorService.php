<?php

declare(strict_types=1);

namespace App\Color\Analyzer;

use InvalidArgumentException;
use League\ColorExtractor\Color;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;

class ColorExtractorService
{
    // Green-Screen color ranges: https://www.rapidtables.com/web/color/Green-Screen-Color-Ranges.html
    //  Typical Chroma-Key green tones: Hue 90°-150°, high saturation, medium lightness
    private const int GREEN_SCREEN_HUE_MIN        = 50;
    private const int GREEN_SCREEN_HUE_MAX        = 150;
    private const float GREEN_SCREEN_SATURATION_MIN = 0.35;
    private const float GREEN_SCREEN_LIGHTNESS_MIN  = 0.03;
    private const float GREEN_SCREEN_LIGHTNESS_MAX  = 0.8;

    /**
     * Extract dominant colors from an image.
     * Returns the primary color and up to 2 secondary colors.
     *
     * @return array{primary: string, secondary: string[]}
     */
    public function extract(string $imagePath): array
    {
        if (!file_exists($imagePath)) {
            throw new InvalidArgumentException(
                sprintf('Image file not found: %s', $imagePath)
            );
        }

        $palette   = Palette::fromFilename($imagePath);
        $extractor = new ColorExtractor($palette);

        // Up to 10 colors are extracted to leave enough colors for Green-Screen filtering
        /** @var array<int, int> $colors */
        $colors = $extractor->extract(10);

        $hasBrightColor = false;
        foreach ($colors as $colorInt) {
            $hex = $this->intToHex($colorInt);
            $hsl = $this->hexToHsl($hex);

            if ($hsl['l'] > 0.35 && $hsl['s'] > 0.1) {
                $hasBrightColor = true;
            }
        }

        $filtered = [];
        foreach ($colors as $colorInt) {
            $hex = $this->intToHex($colorInt);
            if ($this->isGreenScreen($hex)) {
                continue;
            }

            $hsl = $this->hexToHsl($hex);

            // Remove dark shadow colors ONLY if brighter colors exist
            if (
                $hasBrightColor &&
                $hsl['l'] < 0.12
            ) {
                continue;
            }

            $filtered[] = $hex;
        }

        if (empty($filtered)) {
            return ['primary' => '#808080', 'secondary' => []]; // Fallback: Gray
        }

        usort($filtered, function (string $a, string $b) {

            $hslA = $this->hexToHsl($a);
            $hslB = $this->hexToHsl($b);

            $scoreA = ($hslA['s'] * 0.7) + ($hslA['l'] * 0.3);
            $scoreB = ($hslB['s'] * 0.7) + ($hslB['l'] * 0.3);

            return $scoreB <=> $scoreA;
        });

        $primary   = array_shift($filtered);
        $secondary = array_slice($filtered, 0, 2);

        return [
            'primary'   => $primary,
            'secondary' => $secondary,
        ];
    }

    /**
     * Checking for green tones in the HSL color space.
     */
    public function isGreenScreen(string $hexCode): bool
    {
        $hsl = $this->hexToHsl($hexCode);

        return $hsl['h'] >= self::GREEN_SCREEN_HUE_MIN
            && $hsl['h'] <= self::GREEN_SCREEN_HUE_MAX
            && $hsl['s'] >= self::GREEN_SCREEN_SATURATION_MIN
            && $hsl['l'] >= self::GREEN_SCREEN_LIGHTNESS_MIN
            && $hsl['l'] <= self::GREEN_SCREEN_LIGHTNESS_MAX;
    }

    /**
     * Convert a color integer value from league/color-extractor to a HexCode.
     */
    private function intToHex(int $colorInt): string
    {
        return Color::fromIntToHex($colorInt);
    }

    /**
     * Convert a HexCode to HSL (Hue 0-360, Saturation 0-1, Lightness 0-1)
     *
     * @return array{h: float, s: float, l: float}
     */
    private function hexToHsl(string $hex): array
    {
        $hex = ltrim($hex, '#');

        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        $max   = max($r, $g, $b);
        $min   = min($r, $g, $b);
        $delta = $max - $min;

        $l = ($max + $min) / 2;

        if ($delta === 0.0) {
            return ['h' => 0.0, 's' => 0.0, 'l' => $l];
        }

        $s = $delta / (1 - abs(2 * $l - 1));

        $h = 0.0;
        if ($max === $r) {
            $h = 60 * fmod(($g - $b) / $delta, 6);
        } elseif ($max === $g) {
            $h = 60 * (($b - $r) / $delta + 2);
        } else {
            $h = 60 * (($r - $g) / $delta + 4);
        }

        if ($h < 0) {
            $h += 360;
        }

        return ['h' => $h, 's' => $s, 'l' => $l];
    }
}
