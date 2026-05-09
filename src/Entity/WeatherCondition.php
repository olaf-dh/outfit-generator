<?php

namespace App\Entity;

use App\Domain\Outfit\Enum\WeatherConditionType;
use App\Repository\WeatherConditionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WeatherConditionRepository::class)]
class WeatherCondition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: WeatherConditionType::class)]
    private WeatherConditionType $type;

    /**
     * @var Collection<int, ClothingItem>
     */
    #[ORM\ManyToMany(targetEntity: ClothingItem::class, mappedBy: 'weatherConditions')]
    private Collection $clothingItems;

    public function __construct()
    {
        $this->clothingItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): WeatherConditionType
    {
        return $this->type;
    }

    public function setType(WeatherConditionType $type): void
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
}
