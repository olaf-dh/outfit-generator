<?php

declare(strict_types=1);

namespace App\Entity;

use App\ClothingItem\Enum\ClothingItemStatus;
use App\Repository\ClothingItemRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClothingItemRepository::class)]
class ClothingItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $minLayerDepth = null;

    #[ORM\Column]
    private ?int $maxLayerDepth = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photoPath = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $displayPhotoPath = null;

    #[ORM\OneToOne(mappedBy: 'clothingItem', cascade: ['persist'])]
    private ?ColorAnalysis $colorAnalysis = null;

    /**
     * @var Collection<int, ItemColor>
     */
    #[ORM\OneToMany(
        targetEntity: ItemColor::class,
        mappedBy: 'clothingItem',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $itemColors;

    /**
     * @var Collection<int, ItemMaterial>
     */
    #[ORM\OneToMany(
        targetEntity: ItemMaterial::class,
        mappedBy: 'clothingItem',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $itemMaterials;

    /**
     * @var Collection<int, Season>
     */
    #[ORM\ManyToMany(targetEntity: Season::class, inversedBy: 'clothingItems')]
    #[ORM\JoinTable(name: 'clothing_item_season')]
    private Collection $seasons;

    /**
     * @var Collection<int, Style>
     */
    #[ORM\ManyToMany(targetEntity: Style::class, inversedBy: 'clothingItems')]
    #[ORM\JoinTable(name: 'clothing_item_style')]
    private Collection $styles;

    /**
     * @var Collection<int, WeatherCondition>
     */
    #[ORM\ManyToMany(targetEntity: WeatherCondition::class, inversedBy: 'clothingItems')]
    #[ORM\JoinTable(name: 'clothing_item_weather_condition')]
    private Collection $weatherConditions;

    #[ORM\ManyToOne(targetEntity: Pattern::class, inversedBy: 'clothingItems')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Pattern $pattern = null;

    #[ORM\ManyToOne(targetEntity: SubCategory::class, inversedBy: 'clothingItems')]
    private ?SubCategory $subCategory = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'clothingItems')]
    #[ORM\JoinColumn(nullable: false)]
    private User $owner;

    #[ORM\Column(enumType: ClothingItemStatus::class)]
    private ClothingItemStatus $status = ClothingItemStatus::PENDING;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->itemColors = new ArrayCollection();
        $this->itemMaterials = new ArrayCollection();
        $this->seasons = new ArrayCollection();
        $this->styles = new ArrayCollection();
        $this->weatherConditions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getMinLayerDepth(): ?int
    {
        return $this->minLayerDepth;
    }

    public function setMinLayerDepth(int $minLayerDepth): static
    {
        $this->minLayerDepth = $minLayerDepth;

        return $this;
    }

    public function getMaxLayerDepth(): ?int
    {
        return $this->maxLayerDepth;
    }

    public function setMaxLayerDepth(int $maxLayerDepth): static
    {
        $this->maxLayerDepth = $maxLayerDepth;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPhotoPath(): ?string
    {
        return $this->photoPath;
    }

    public function setPhotoPath(?string $photoPath): static
    {
        $this->photoPath = $photoPath;

        return $this;
    }

    public function getDisplayPhotoPath(): ?string
    {
        return $this->displayPhotoPath;
    }

    public function setDisplayPhotoPath(?string $displayPhotoPath): static
    {
        $this->displayPhotoPath = $displayPhotoPath;

        return $this;
    }

    public function getColorAnalysis(): ?ColorAnalysis
    {
        return $this->colorAnalysis;
    }

    public function setColorAnalysis(?ColorAnalysis $colorAnalysis): static
    {
        if ($this->colorAnalysis === $colorAnalysis) {
            return $this;
        }
        if ($this->colorAnalysis !== null && $this->colorAnalysis->getClothingItem() === $this) {
            $this->colorAnalysis->setClothingItem(null);
        }

        $this->colorAnalysis = $colorAnalysis;

        if ($colorAnalysis !== null && $colorAnalysis->getClothingItem() !== $this) {
            $colorAnalysis->setClothingItem($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, ItemColor>
     */
    public function getItemColors(): Collection
    {
        return $this->itemColors;
    }

    public function addItemColor(ItemColor $itemColor): static
    {
        if (!$this->itemColors->contains($itemColor)) {
            $this->itemColors->add($itemColor);
            $itemColor->setClothingItem($this);
        }

        return $this;
    }

    public function removeItemColor(ItemColor $itemColor): static
    {
        if ($this->itemColors->removeElement($itemColor)) {
            // set the owning side to null (unless already changed)
            if ($itemColor->getClothingItem() === $this) {
                $itemColor->setClothingItem(null);
            }
        }

        return $this;
    }

    public function updatePrimaryColor(?Color $newPrimary): void
    {
        if ($newPrimary === null) {
            return;
        }

        if ($this->itemColors->isEmpty()) {
            return;
        }

        foreach ($this->itemColors as $itemColor) {
            $itemColor->setIsPrimary($itemColor->getColor()->getId() === $newPrimary->getId());
        }
    }

    /**
     * @return Collection<int, ItemMaterial>
     */
    public function getItemMaterials(): Collection
    {
        return $this->itemMaterials;
    }

    public function addItemMaterial(ItemMaterial $itemMaterial): static
    {
        if (!$this->itemMaterials->contains($itemMaterial)) {
            $this->itemMaterials->add($itemMaterial);
            $itemMaterial->setClothingItem($this);
        }

        return $this;
    }

    public function removeItemMaterial(ItemMaterial $itemMaterial): static
    {
        if ($this->itemMaterials->removeElement($itemMaterial)) {
            // set the owning side to null (unless already changed)
            if ($itemMaterial->getClothingItem() === $this) {
                $itemMaterial->setClothingItem(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Season>
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    public function addSeason(Season $season): static
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons->add($season);
        }

        return $this;
    }

    public function removeSeason(Season $season): static
    {
        $this->seasons->removeElement($season);

        return $this;
    }

    /**
     * @return Collection<int, Style>
     */
    public function getStyles(): Collection
    {
        return $this->styles;
    }

    public function addStyle(Style $style): static
    {
        if (!$this->styles->contains($style)) {
            $this->styles->add($style);
        }

        return $this;
    }

    public function removeStyle(Style $style): static
    {
        $this->styles->removeElement($style);

        return $this;
    }

    public function addWeatherCondition(WeatherCondition $weather): static
    {
        if (!$this->weatherConditions->contains($weather)) {
            $this->weatherConditions->add($weather);
        }
        return $this;
    }

    /**
     * @return Collection<int, WeatherCondition>
     */
    public function getWeatherConditions(): Collection
    {
        return $this->weatherConditions;
    }

    public function removeWeatherCondition(WeatherCondition $weather): static
    {
        $this->weatherConditions->removeElement($weather);
        return $this;
    }

    public function getSubCategory(): ?SubCategory
    {
        return $this->subCategory;
    }

    public function setSubCategory(?SubCategory $subCategory): static
    {
        $this->subCategory = $subCategory;

        return $this;
    }

    public function getPattern(): ?Pattern
    {
        return $this->pattern;
    }

    public function setPattern(?Pattern $pattern): static
    {
        $this->pattern = $pattern;

        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getStatus(): ClothingItemStatus
    {
        return $this->status;
    }

    public function setStatus(ClothingItemStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? 'ClothingItem #' . $this->getId();
    }
}
