<?php

namespace App\DTO\Outfit;

use App\Entity\ClothingItem;

final class OutfitSuggestion
{
    /** @var ClothingItem[] */
    private array $items = [];

    private int $score = 0;

    /** @var string[]  */
    private array $reasons = [];

    public function addItem(ClothingItem $item): static
    {
        $this->items[] = $item;
        return $this;
    }

    /** @return ClothingItem[] */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;
        return $this;
    }

    public function addScore(int $points): static
    {
        return $this->incrementScore($points);
    }

    public function incrementScore(int $points = 1): static
    {
        $this->score += $points;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getReasons(): array
    {
        return $this->reasons;
    }

    public function addReason(string $reason): static
    {
        if (!in_array($reason, $this->reasons, true)) {
            $this->reasons[] = $reason;
        }

        return $this;
    }

    /**
     * @param string[] $reasons
     */
    public function addReasons(array $reasons): static
    {
        foreach ($reasons as $reason) {
            $this->addReason($reason);
        }

        return $this;
    }

    public function count(): int
    {
        return count($this->items);
    }
}
