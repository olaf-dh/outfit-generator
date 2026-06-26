<?php

declare(strict_types=1);

namespace App\Entity;

use App\ClothingItem\Enum\BreathabilityLevel;
use App\ClothingItem\Enum\MaterialCategory;
use App\ClothingItem\Enum\WarmthLevel;
use App\Repository\MaterialRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MaterialRepository::class)]
class Material extends AbstractAttribute
{
    #[ORM\Column(enumType: MaterialCategory::class)]
    private MaterialCategory $category;

    #[ORM\Column(enumType: WarmthLevel::class)]
    private WarmthLevel $warmth;

    #[ORM\Column(enumType: BreathabilityLevel::class)]
    private BreathabilityLevel $breathability;

    #[ORM\Column]
    private bool $waterproof = false;

    #[ORM\Column]
    private bool $stretch = false;

    #[ORM\Column]
    private bool $windproof = false;

    public function getCategory(): MaterialCategory
    {
        return $this->category;
    }

    public function setCategory(MaterialCategory $category): void
    {
        $this->category = $category;
    }

    public function getWarmth(): WarmthLevel
    {
        return $this->warmth;
    }

    public function setWarmth(WarmthLevel $warmth): void
    {
        $this->warmth = $warmth;
    }

    public function getBreathability(): BreathabilityLevel
    {
        return $this->breathability;
    }

    public function setBreathability(BreathabilityLevel $breathability): void
    {
        $this->breathability = $breathability;
    }

    public function isWaterproof(): bool
    {
        return $this->waterproof;
    }

    public function setWaterproof(bool $waterproof): void
    {
        $this->waterproof = $waterproof;
    }

    public function isStretch(): bool
    {
        return $this->stretch;
    }

    public function setStretch(bool $stretch): void
    {
        $this->stretch = $stretch;
    }

    public function isWindproof(): bool
    {
        return $this->windproof;
    }

    public function setWindproof(bool $windproof): void
    {
        $this->windproof = $windproof;
    }


    public function __toString(): string
    {
        return $this->name ?? 'Material #' . $this->id;
    }
}
