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
class Color extends AbstractAttribute
{
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

    #[ORM\Column(type: 'smallint')]
    private int $r;

    #[ORM\Column(type: 'smallint')]
    private int $g;

    #[ORM\Column(type: 'smallint')]
    private int $b;

    #[ORM\Column]
    private int $h;

    #[ORM\Column]
    private int $s;

    #[ORM\Column]
    private int $v;

    /**
     * @var Collection<int, ItemColor>
     */
    #[ORM\OneToMany(targetEntity: ItemColor::class, mappedBy: 'color', cascade: ['persist', 'remove'])]
    private Collection $itemColors;

    public function __construct()
    {
        $this->itemColors = new ArrayCollection();
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

    public function getR(): int
    {
        return $this->r;
    }

    public function setR(int $r): static
    {
        $this->r = $r;

        return $this;
    }

    public function getG(): int
    {
        return $this->g;
    }

    public function setG(int $g): static
    {
        $this->g = $g;

        return $this;
    }

    public function getB(): int
    {
        return $this->b;
    }

    public function setB(int $b): static
    {
        $this->b = $b;

        return $this;
    }

    public function getH(): int
    {
        return $this->h;
    }

    public function setH(int $h): static
    {
        $this->h = $h;

        return $this;
    }

    public function getS(): int
    {
        return $this->s;
    }

    public function setS(int $s): static
    {
        $this->s = $s;

        return $this;
    }

    public function getV(): int
    {
        return $this->v;
    }

    public function setV(int $v): static
    {
        $this->v = $v;

        return $this;
    }
}
