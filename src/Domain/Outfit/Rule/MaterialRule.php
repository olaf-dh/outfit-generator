<?php

declare(strict_types=1);

namespace App\Domain\Outfit\Rule;

use App\Domain\Outfit\DTO\OutfitSuggestion;
use App\Domain\Outfit\Enum\WarmthLevel;
use App\Domain\Outfit\Generator\OutfitContext;
use App\Entity\ItemMaterial;

/**
 * Evaluates materials according to:
 * - Temperature
 * - Weather
 * - Layering
 * - Material properties
 */
final class MaterialRule implements OutfitRuleInterface
{
    public function apply(OutfitSuggestion $suggestion, OutfitContext $context): OutfitSuggestion
    {
        $score = 0;
        $reasons = [];

        $allMaterials = $this->extractMaterials($suggestion);

        if ($allMaterials === []) {
            return $suggestion;
        }

        // 🌡️ Temperature evaluation
        $warmthScore = $this->evaluateWarmth(
            $allMaterials,
            $context->temperature
        );

        $score += $warmthScore;

        if ($warmthScore < 0) {
            $reasons[] = 'material.rule.temperature.bad';
        }

        if ($warmthScore > 0) {
            $reasons[] = 'material.rule.temperature.good';
        }

        // 🌧 Weather evaluation
        if ($context->isRaining) {
            $weatherScore = $this->evaluateRainSuitability($allMaterials);

            $score += $weatherScore;

            if ($weatherScore > 0) {
                $reasons[] = 'material.rule.rain.good';
            }

            if ($weatherScore < 0) {
                $reasons[] = 'material.rule.rain.bad';
            }
        }

        // 💨 Wind evaluation
        if ($context->isWindy) {
            $windScore = $this->evaluateWindSuitability($allMaterials);

            $score += $windScore;

            if ($windScore > 0) {
                $reasons[] = 'material.rule.wind.good';
            }

            if ($windScore < 0) {
                $reasons[] = 'material.rule.wind.bad';
            }
        }

        // 🧥 Layering-Check
        $layerScore = $this->evaluateLayering($allMaterials);

        $score += $layerScore;

        if ($layerScore < 0) {
            $reasons[] = 'material.rule.layering.bad';
        }

        return $suggestion
            ->addScore($score)
            ->addReasons($reasons);
    }

    /**
     * @return ItemMaterial[]
     */
    private function extractMaterials(OutfitSuggestion $suggestion): array
    {
        $materials = [];

        foreach ($suggestion->getItems() as $item) {
            foreach ($item->getItemMaterials() as $itemMaterial) {
                $materials[] = $itemMaterial;
            }
        }

        return $materials;
    }

    /**
     * 🌡 Temperature rule
     *
     * @param ItemMaterial[] $materials
     * @param int $temperature
     * @return int
     */
    private function evaluateWarmth(array $materials, int $temperature): int
    {
        $warmth = $this->calculateWarmthLevel($materials);

        // hot
        if ($temperature >= 28) {
            return match ($warmth) {
                WarmthLevel::LOW => 12,
                WarmthLevel::MEDIUM => -4,
                WarmthLevel::HIGH => -20,
            };
        }

        // warm
        if ($temperature >= 18) {
            return match ($warmth) {
                WarmthLevel::LOW => 8,
                WarmthLevel::MEDIUM => 5,
                WarmthLevel::HIGH => -10,
            };
        }

        // cool
        if ($temperature >= 8) {
            return match ($warmth) {
                WarmthLevel::LOW => -5,
                WarmthLevel::MEDIUM => 10,
                WarmthLevel::HIGH => 5,
            };
        }

        // cold
        return match ($warmth) {
            WarmthLevel::LOW => -25,
            WarmthLevel::MEDIUM => 5,
            WarmthLevel::HIGH => 15,
        };
    }

    /**
     * 🌧 Rain rule
     *
     * @param ItemMaterial[] $materials
     * return int
     */
    private function evaluateRainSuitability(array $materials): int
    {
        $waterproofCount = 0;

        foreach ($materials as $material) {
            if ($material->getMaterial()->isWaterproof()) {
                $waterproofCount++;
            }
        }

        if ($waterproofCount >= 1) {
            return 10;
        }

        return -10;
    }

    /**
     * 💨 Wind rule
     *
     * @param ItemMaterial[] $materials
     * return int
     */
    private function evaluateWindSuitability(array $materials): int
    {
        $windproofCount = 0;

        foreach ($materials as $material) {
            if ($material->getMaterial()->isWindproof()) {
                $windproofCount++;
            }
        }

        if ($windproofCount >= 1) {
            return 8;
        }

        return -8;
    }

    /**
     * 🧥 Layering rule
     *
     * @param ItemMaterial[] $materials
     * return int
     *
     */
    private function evaluateLayering(array $materials): int
    {
        $heavyMaterials = 0;

        foreach ($materials as $material) {
            if (
                $material->getMaterial()->getWarmth() === WarmthLevel::HIGH
            ) {
                $heavyMaterials++;
            }
        }

        // to many heavy materials
        if ($heavyMaterials >= 3) {
            return -15;
        }

        return 5;
    }

    /**
     * @param ItemMaterial[] $materials
     * @return WarmthLevel
     */
    private function calculateWarmthLevel(array $materials): WarmthLevel
    {
        $values = array_map(
            static function (ItemMaterial $itemMaterial): int {
                return match ($itemMaterial->getMaterial()->getWarmth()) {
                    WarmthLevel::LOW => 1,
                    WarmthLevel::MEDIUM => 2,
                    WarmthLevel::HIGH => 3,
                };
            },
            $materials
        );

        $average = array_sum($values) / count($values);

        return match (true) {
            $average < 1.75 => WarmthLevel::LOW,
            $average < 2.5 => WarmthLevel::MEDIUM,
            default => WarmthLevel::HIGH,
        };
    }
}
