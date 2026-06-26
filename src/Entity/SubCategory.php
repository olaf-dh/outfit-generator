<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SubCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubCategoryRepository::class)]
class SubCategory extends AbstractAttribute
{
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'subCategories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    /**
     * @var Collection<int, ClothingItem>
     */
    #[ORM\OneToMany(targetEntity: ClothingItem::class, mappedBy: 'subCategory')]
    private Collection $clothingItems;

    public function __construct()
    {
        $this->clothingItems = new ArrayCollection();
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, ClothingItem>
     */
    public function getClothingItems(): Collection
    {
        return $this->clothingItems;
    }

    public function addClothingItem(ClothingItem $clothingItem): static
    {
        if (!$this->clothingItems->contains($clothingItem)) {
            $this->clothingItems->add($clothingItem);
            $clothingItem->setSubCategory($this);
        }

        return $this;
    }

    public function removeClothingItem(ClothingItem $clothingItem): static
    {
        if ($this->clothingItems->removeElement($clothingItem)) {
            // set the owning side to null (unless already changed)
            if ($clothingItem->getSubCategory() === $this) {
                $clothingItem->setSubCategory(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
