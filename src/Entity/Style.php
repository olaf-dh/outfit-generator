<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\StyleType;
use App\Repository\StyleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StyleRepository::class)]
class Style
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: StyleType::class)]
    private StyleType $type;

    /**
     * @var Collection<int, ClothingItem>
     */
    #[ORM\ManyToMany(targetEntity: ClothingItem::class, mappedBy: 'styles')]
    private Collection $clothingItems;


    public function __construct()
    {
        $this->clothingItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): StyleType
    {
        return $this->type;
    }

    public function setType(StyleType $type): void
    {
        $this->type = $type;
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
