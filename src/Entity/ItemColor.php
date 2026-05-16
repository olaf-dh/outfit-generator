<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ItemColorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemColorRepository::class)]
class ItemColor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ClothingItem::class, inversedBy: 'itemColors')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ClothingItem $clothingItem = null;

    #[ORM\ManyToOne(targetEntity: Color::class, inversedBy: 'itemColors')]
    #[ORM\JoinColumn(nullable: false)]
    private Color $color;

    #[ORM\Column(options: ['default' => false])]
    private bool $isPrimary = false;

    /**
     * @param ClothingItem $clothingItem
     * @param Color $color
     * @param bool $isPrimary
     */
    public function __construct(ClothingItem $clothingItem, Color $color, bool $isPrimary = false)
    {
        $this->clothingItem = $clothingItem;
        $this->color = $color;
        $this->isPrimary = $isPrimary;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClothingItem(): ?ClothingItem
    {
        return $this->clothingItem;
    }

    public function setClothingItem(?ClothingItem $clothingItem): static
    {
        $this->clothingItem = $clothingItem;

        return $this;
    }

    public function getColor(): Color
    {
        return $this->color;
    }

    public function setColor(Color $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function setIsPrimary(bool $isPrimary): static
    {
        $this->isPrimary = $isPrimary;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->color->getName(), $this->isPrimary ? 'primary' : 'secondary');
    }
}
