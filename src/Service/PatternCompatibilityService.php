<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Outfit\Enum\PatternType;
use App\Entity\Pattern;

class PatternCompatibilityService
{
    private const array GEOMETRIC_TYPES = [
        PatternType::VERTICAL_STRIPES,
        PatternType::HORIZONTAL_STRIPES,
        PatternType::CHECKED,
        PatternType::DOTTED,
    ];

    private const array ORGANIC_TYPES = [
        PatternType::FLORAL,
        PatternType::LEAF,
    ];

    private const array STATEMENT_TYPES = [
        PatternType::PRINT,
        PatternType::NOVELTY,
        PatternType::MULTICOLOR,
    ];

    public function areCompatible(Pattern $a, Pattern $b): bool
    {
        // Rule 1: solid is compatible with all patterns
        if ($this->isSolid($a) || $this->isSolid($b)) {
            return true;
        }

        // Rule 2: Statement only applies to solid colors
        // so Statement + anything else = incompatible
        if ($this->isStatement($a) || $this->isStatement($b)) {
            return false;
        }

        // Rule 3: Geometric + Organic = not compatible
        if ($this->isGeometric($a) && $this->isOrganic($b)) {
            return false;
        }
        if ($this->isOrganic($a) && $this->isGeometric($b)) {
            return false;
        }

        // Rule 4: Geometric + Geometric = not compatible
        if ($this->isGeometric($a) && $this->isGeometric($b)) {
            return false;
        }

        // Rule 5: Organic + Organic = compatible
        if ($this->isOrganic($a) && $this->isOrganic($b)) {
            return true;
        }

        return false;
    }

    private function isSolid(Pattern $pattern): bool
    {
        return $pattern->getType() === PatternType::SOLID;
    }

    private function isGeometric(Pattern $pattern): bool
    {
        return in_array($pattern->getType(), self::GEOMETRIC_TYPES, true);
    }

    private function isOrganic(Pattern $pattern): bool
    {
        return in_array($pattern->getType(), self::ORGANIC_TYPES, true);
    }

    private function isStatement(Pattern $pattern): bool
    {
        return in_array($pattern->getType(), self::STATEMENT_TYPES, true);
    }
}
