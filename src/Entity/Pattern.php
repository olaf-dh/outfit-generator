<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Outfit\Enum\PatternType;
use App\Repository\PatternRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PatternRepository::class)]
class Pattern
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: PatternType::class)]
    private PatternType $type;

    #[ORM\Column]
    private int $maxSecondaryColor = 0;

    /**
     * @var Collection<int, ClothingItem>
     */
    #[ORM\OneToMany(targetEntity: ClothingItem::class, mappedBy: 'pattern')]
    private Collection $clothingItems;

    public function __construct()
    {
        $this->clothingItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): PatternType
    {
        return $this->type;
    }

    public function setType(PatternType $type): void
    {
        $this->type = $type;
    }

    public function getMaxSecondaryColor(): int
    {
        return $this->maxSecondaryColor;
    }

    public function setMaxSecondaryColor(int $maxSecondaryColor): void
    {
        $this->maxSecondaryColor = $maxSecondaryColor;
    }

    /**
     * @return Collection<int, ClothingItem>
     */
    public function getClothingItems(): Collection
    {
        return $this->clothingItems;
    }

    public function __toString(): string
    {
        return $this->type->value ?? '';
    }
}
