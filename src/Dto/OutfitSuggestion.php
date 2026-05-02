<?php

namespace App\Dto;

use App\Entity\ClothingItem;

class OutfitSuggestion
{
    /** @var ClothingItem[] */
    private array $items = [];

    private int $score = 0;

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

    public function incrementScore(int $points = 1): static
    {
        $this->score += $points;
        return $this;
    }

    public function count(): int
    {
        return count($this->items);
    }
}
