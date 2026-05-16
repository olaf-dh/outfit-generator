<?php

declare(strict_types=1);

namespace App\Outfit\Rule;

use App\ClothingItem\Enum\StyleType;
use App\Entity\Style;

class StyleCompatibilityService
{
    private const array COMPATIBILITY_MAP = [
        StyleType::CASUAL->value => [
            StyleType::CASUAL->value,
            StyleType::SMART_CASUAL->value,
        ],
        StyleType::SMART_CASUAL->value => [
            StyleType::CASUAL->value,
            StyleType::SMART_CASUAL->value,
            StyleType::BUSINESS->value,
        ],
        StyleType::BUSINESS->value => [
            StyleType::SMART_CASUAL->value,
            StyleType::BUSINESS->value,
        ],
    ];

    public function areCompatible(Style $style1, Style $style2): bool
    {
        $aValue = $style1->getType()->value;
        $bValue = $style2->getType()->value;

        return in_array($bValue, self::COMPATIBILITY_MAP[$aValue] ?? [], true);
    }
}
