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
        public int $h,
        public int $s,
        public int $v,
        public float $weight,
    ) {
    }
}
