<?php

declare(strict_types=1);

namespace App\DTO\Color;

final class ExtractedColor
{
    public function __construct(
        public string $hex,
        public int $r,
        public int $g,
        public int $b,
        public float $h,
        public float $s,
        public float $v,
        public float $weight,
    ) {
    }
}
