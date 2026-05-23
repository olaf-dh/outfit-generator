<?php

declare(strict_types=1);

namespace App\Color\Service;

class ColorConverterService
{
    /**
     * @param string $hex
     * @return array<string, float|int>
     */
    public function hexToHsv(string $hex): array
    {
        $rgb = $this->hexToRgb($hex);

        $r = $rgb['r'] / 255;
        $g = $rgb['g'] / 255;
        $b = $rgb['b'] / 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);

        $delta = $max - $min;

        $h = 0.0;

        if (abs($delta) > 0.00001) {
            if ($max === $r) {
                $h = 60 * fmod(
                    (($g - $b) / $delta),
                    6
                );
            } elseif ($max === $g) {
                $h = 60 * (
                        (($b - $r) / $delta) + 2
                    );
            } else {
                $h = 60 * (
                        (($r - $g) / $delta) + 4
                    );
            }
        }

        if ($h < 0) {
            $h += 360;
        }

        $s = $max <= 0.00001
            ? 0.0
            : $delta / $max;

        $v = $max;

        return [
            'h' => $h,
            's' => $s,
            'v' => $v,
        ];
    }

    /**
     * HexCode → RGB (0-255)
     * @return array<string, int>
     */
    public function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        return [
            'r' => (int) hexdec(substr($hex, 0, 2)),
            'g' => (int) hexdec(substr($hex, 2, 2)),
            'b' => (int) hexdec(substr($hex, 4, 2)),
        ];
    }
}
