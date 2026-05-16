<?php

declare(strict_types=1);

namespace App\Entity;

use App\Color\Enum\ColorFamily;
use App\Color\Enum\ColorSaturation;
use App\Color\Enum\ColorTemperature;
use App\Color\Enum\ColorTone;
use App\Repository\ColorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ColorRepository::class)]
class Color
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private string $name;

    #[ORM\Column(length: 7, nullable: false)]
    #[Assert\Regex(
        pattern: '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
        message: 'The value {{ value }} is not a valid hex code.'
    )]
    private string $hexCode;

    #[ORM\Column(enumType: ColorFamily::class)]
    private ColorFamily $family;

    #[ORM\Column(enumType: ColorTone::class)]
    private ColorTone $tone;

    #[ORM\Column(enumType: ColorTemperature::class)]
    private ColorTemperature $temperature;

    #[ORM\Column(enumType: ColorSaturation::class)]
    private ColorSaturation $saturation;

    /**
     * @var Collection<int, ItemColor>
     */
    #[ORM\OneToMany(targetEntity: ItemColor::class, mappedBy: 'color', cascade: ['persist', 'remove'])]
    private Collection $itemColors;

    public function __construct()
    {
        $this->itemColors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getHexCode(): string
    {
        return $this->hexCode;
    }

    public function setHexCode(string $hexCode): static
    {
        $this->hexCode = $hexCode;

        return $this;
    }

    public function getFamily(): ?ColorFamily
    {
        return $this->family;
    }

    public function setFamily(ColorFamily $family): static
    {
        $this->family = $family;

        return $this;
    }

    public function getTone(): ?ColorTone
    {
        return $this->tone;
    }

    public function setTone(ColorTone $tone): static
    {
        $this->tone = $tone;

        return $this;
    }

    public function getTemperature(): ?ColorTemperature
    {
        return $this->temperature;
    }

    public function setTemperature(ColorTemperature $temperature): static
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getSaturation(): ?ColorSaturation
    {
        return $this->saturation;
    }

    public function setSaturation(ColorSaturation $saturation): static
    {
        $this->saturation = $saturation;

        return $this;
    }
}
