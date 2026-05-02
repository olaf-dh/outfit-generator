<?php

namespace App\Service;

use App\Entity\Color;
use App\Enum\ColorFamily;
use App\Enum\ColorSaturation;
use App\Enum\ColorTemperature;

class ColorCompatibilityService
{
    private const array NEUTRAL_FAMILIES = [
        ColorFamily::BLACK,
        ColorFamily::WHITE,
        ColorFamily::GRAY,
        ColorFamily::NAVY,
        ColorFamily::BEIGE,
    ];

    public function areCompatible(Color $color1, Color $color2): bool
    {
        // Rule 1: Two vivid colors are always incompatible
        if ($this->areBothVivid($color1, $color2)) {
            return false;
        }

        // Rule 2: Colors of the same family are compatible - Tone must be different
        if ($color1->getFamily() === $color2->getFamily()) {
            return $color1->getTone() !== $color2->getTone();
        }

        // Rule 3: Neutral colors are compatible with all other colors
        if ($this->isNeutral($color1) || $this->isNeutral($color2)) {
            return true;
        }

        // Rule 4: Temperature - warm+warm or cool+cool is harmonious
        return $color1->getTemperature() === $color2->getTemperature()
            || $color1->getTemperature() === ColorTemperature::NEUTRAL
            || $color2->getTemperature() === ColorTemperature::NEUTRAL;
    }

    private function isNeutral(Color $color): bool
    {
        return in_array($color->getFamily(), self::NEUTRAL_FAMILIES, true);
    }

    private function areBothVivid(Color $a, Color $b): bool
    {
        return $a->getSaturation() === ColorSaturation::VIVID
            && $b->getSaturation() === ColorSaturation::VIVID;
    }
}
