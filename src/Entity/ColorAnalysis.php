<?php

declare(strict_types=1);

namespace App\Entity;

use App\DTO\Color\ExtractedColor;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ColorAnalysis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * @var array<ExtractedColor>
     */
    #[ORM\Column(type: 'json')]
    private array $extractedColors = [];

    #[ORM\OneToOne(targetEntity: ClothingItem::class, inversedBy: 'colorAnalysis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ClothingItem $clothingItem = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return array<ExtractedColor>
     */
    public function getExtractedColors(): array
    {
        return $this->extractedColors;
    }

    /**
     * @param array<ExtractedColor> $extractedColors
     * @return $this
     */
    public function setExtractedColors(array $extractedColors): static
    {
        $this->extractedColors = $extractedColors;

        return $this;
    }

    public function getClothingItem(): ?ClothingItem
    {
        return $this->clothingItem;
    }

    public function setClothingItem(?ClothingItem $clothingItem): static
    {
        if ($this->clothingItem === $clothingItem) {
            return $this;
        }
        if ($this->clothingItem !== null && $this->clothingItem->getColorAnalysis() === $this) {
            $this->clothingItem->setColorAnalysis(null);
        }
        $this->clothingItem = $clothingItem;

        if ($clothingItem !== null && $clothingItem->getColorAnalysis() !== $this) {
            $clothingItem->setColorAnalysis($this);
        }

        return $this;
    }
}
