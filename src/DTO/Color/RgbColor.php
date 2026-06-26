<?php

declare(strict_types=1);

namespace App\DTO\Color;

final class RgbColor
{
    public function __construct(
        public int $r,
        public int $g,
        public int $b,
    ) {
    }
}
