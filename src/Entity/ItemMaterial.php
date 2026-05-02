<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ItemMaterialRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ItemMaterialRepository::class)]
class ItemMaterial
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ClothingItem::class, inversedBy: 'itemMaterials')]
    #[ORM\JoinColumn(nullable: false)]
    private ClothingItem $clothingItem;

    #[ORM\ManyToOne(targetEntity: Material::class, inversedBy: 'itemMaterials')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Material $material = null;

    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\LessThanOrEqual(100)]
    private ?float $percentage = null;

    /**
     * @param ClothingItem $clothingItem
     * @param Material $material
     * @param float|null $percentage
     */
    public function __construct(ClothingItem $clothingItem, Material $material, ?float $percentage = null)
    {
        $this->clothingItem = $clothingItem;
        $this->material = $material;
        $this->percentage = $percentage;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClothingItem(): ClothingItem
    {
        return $this->clothingItem;
    }

    public function setClothingItem(ClothingItem $clothingItem): static
    {
        $this->clothingItem = $clothingItem;

        return $this;
    }

    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    public function setMaterial(?Material $material): static
    {
        $this->material = $material;

        return $this;
    }

    public function getPercentage(): ?float
    {
        return $this->percentage;
    }

    public function setPercentage(?float $percentage): static
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function isDominant(): bool
    {
        return ($this->percentage ?? 0) >= 50;
    }

    public function __toString(): string
    {
        if ($this->material === null) {
            throw new \LogicException('Material must not be null in ItemMaterial::__toString().');
        }

        return sprintf('%s (%s%%)', $this->material->getName(), $this->percentage ?? 0);
    }
}
