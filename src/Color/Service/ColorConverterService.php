<?php

declare(strict_types=1);

namespace App\Color\Service;

use App\DTO\Color\HsvColor;
use App\DTO\Color\RgbColor;
use InvalidArgumentException;

class ColorConverterService
{
    /**
     * @param string $hex
     * @return HsvColor
     */
    public function hexToHsv(string $hex): HsvColor
    {
        $rgb = $this->hexToRgb($hex);

        $r = $rgb->r / 255;
        $g = $rgb->g / 255;
        $b = $rgb->b / 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $delta = $max - $min;

        $v = $max;
        $s = $max > 0 ? $delta / $max : 0;

        $h = 0;
        if ($delta > 0) {
            if (abs($max - $r) < PHP_FLOAT_EPSILON) {
                $h = 60 * fmod((($g - $b) / $delta), 6);
            } elseif (abs($max - $g) < PHP_FLOAT_EPSILON) {
                $h = 60 * ((($b - $r) / $delta) + 2);
            } else {
                $h = 60 * ((($r - $g) / $delta) + 4);
            }
            if ($h < 0) {
                $h += 360;
            }
        }

        return new HsvColor(
            h: (int) round($h),                 // 0 - 360°
            s: (int) round($s * 100),     // 0 - 100%
            v: (int) round($v * 100),     // 0 - 100%
        );
    }

    /**
     * HexCode → RGB (0-255)
     * @param string $hex
     * @return RgbColor
     */
    public function hexToRgb(string $hex): RgbColor
    {
        if (strlen($hex) !== 7) {
            throw new InvalidArgumentException('Invalid hex code');
        }

        if (!str_starts_with($hex, '#')) {
            throw new InvalidArgumentException('Invalid hex code');
        }
        $hex = ltrim($hex, '#');
        if (!ctype_xdigit($hex)) {
            throw new InvalidArgumentException('Invalid hex code');
        }

        return new RgbColor(
            r: (int) hexdec(substr($hex, 0, 2)),
            g: (int) hexdec(substr($hex, 2, 2)),
            b: (int) hexdec(substr($hex, 4, 2))
        );
    }
}
