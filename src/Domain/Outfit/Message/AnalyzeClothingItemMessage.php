<?php

declare(strict_types=1);

namespace App\Domain\Outfit\Message;

readonly class AnalyzeClothingItemMessage
{
    public function __construct(private int $clothingItemId = 0)
    {
    }

    public function getClothingItemId(): int
    {
        return $this->clothingItemId;
    }
}
