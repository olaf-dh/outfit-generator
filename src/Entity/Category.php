<?php

declare(strict_types=1);

namespace App\Entity;

use App\ClothingItem\Enum\BodyZone;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category extends AbstractAttribute
{
    #[ORM\Column(enumType: BodyZone::class)]
    private BodyZone $bodyZone;

    /**
     * @var Collection<int, SubCategory>
     */
    #[ORM\OneToMany(
        targetEntity: SubCategory::class,
        mappedBy: 'category',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $subCategories;

    public function __construct()
    {
        $this->subCategories = new ArrayCollection();
    }

    public function getBodyZone(): BodyZone
    {
        return $this->bodyZone;
    }

    public function setBodyZone(BodyZone $bodyZone): static
    {
        $this->bodyZone = $bodyZone;

        return $this;
    }

    /**
     * @return Collection<int, SubCategory>
     */
    public function getSubCategories(): Collection
    {
        return $this->subCategories;
    }

    public function addSubCategory(SubCategory $subCategory): static
    {
        if (!$this->subCategories->contains($subCategory)) {
            $this->subCategories->add($subCategory);
            $subCategory->setCategory($this);
        }

        return $this;
    }

    public function removeSubCategory(SubCategory $subCategory): static
    {
        if ($this->subCategories->removeElement($subCategory)) {
            // set the owning side to null (unless already changed)
            if ($subCategory->getCategory() === $this) {
                $subCategory->setCategory(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? 'Category #' . $this->id;
    }
}
