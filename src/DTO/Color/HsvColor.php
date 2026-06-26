<?php

declare(strict_types=1);

namespace App\DTO\Color;

final class HsvColor
{
    public function __construct(
        public int $h,
        public int $s,
        public int $v,
    ) {
    }
}
