<?php

declare(strict_types=1);

namespace App\Color\Resolver;

use App\DTO\Color\ExtractedColor;

final class ColorFamilyResolver
{
    private array $familyRanges = [
        'black' => [0, 360, 0, 10, 0, 15],
        'white' => [0, 360, 0, 10, 70, 100],
        'gray' => [0, 360, 0, 45, 10, 75],
        'beige' => [25, 60, 5, 50, 65, 100],
        'brown' => [0, 45, 30, 100, 25, 85],
        'pink' => [320, 360, 15, 95, 70, 100],
        'red' => [345, 17, 40, 100, 50, 100],
        'orange' => [5, 45, 25, 100, 70, 100],
        'yellow' => [25, 85, 25, 100, 30, 100],
        'green' => [60, 180, 20, 100, 30, 100],
        'blue' => [195, 275, 20, 100, 40, 100],
        'purple' => [240, 330, 5, 100, 20, 100],
    ];

    /**
     * @param ExtractedColor $color
     * @return list<string>
     */
    public function resolve(ExtractedColor $color): array
    {
        $h = $color->h;  // 0–360°
        $s = $color->s;  // 0–100%
        $v = $color->v;  // 0–100%

        $families = [];

        foreach ($this->familyRanges as $family => $range) {
            if ($this->isInRange($h, $s, $v, $range)) {
                $families[] = $family;
            }
        }
        $families = array_values(array_unique($families));

        if ($families !== []) {
            return $families;
        }

        // Fallback to pink
        return ['pink'];
    }

    /**
     * @param int $h
     * @param int $s
     * @param int $v
     * @param array<string, list<int>> $range
     * @return bool
     */
    private function isInRange(int $h, int $s, int $v, array $range): bool
    {
        list($minH, $maxH, $minS, $maxS, $minV, $maxV) = $range;

        $hInRange = false;
        if ($minH <= $maxH) {
            $hInRange = ($h >= $minH && $h <= $maxH);
        } else {
            $hInRange = ($h >= $minH || $h <= $maxH);
        }

        $sInRange = ($s >= $minS && $s <= $maxS);
        $vInRange = ($v >= $minV && $v <= $maxV);

        return $hInRange && $sInRange && $vInRange;
    }
}
