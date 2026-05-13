<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Outfit\DTO\OutfitSuggestion;
use App\Domain\Outfit\Enum\BodyZone;
use App\Domain\Outfit\Enum\SeasonType;
use App\Domain\Outfit\Enum\StyleType;
use App\Domain\Outfit\Enum\WeatherConditionType;
use App\Entity\ClothingItem;
use App\Entity\Style;
use App\Repository\ClothingItemRepository;

class OutfitSuggestionService
{
    private const array REQUIRED_ZONES = [
        BodyZone::UPPER_BODY,
        BodyZone::LOWER_BODY,
        BodyZone::FOOTWEAR,
    ];

//    private const array OPTIONAL_ZONES = [
//        BodyZone::OUTER_LAYER,
//        BodyZone::HEAD,
//        BodyZone::ACCESSORY,
//    ];

    public function __construct(
        private readonly ClothingItemRepository $clothingItemRepository,
        private readonly ZoneResolverService $zoneResolver,
        private readonly ColorCompatibilityService $colorCompatibility,
        private readonly PatternCompatibilityService $patternCompatibility,
        private readonly SeasonCompatibilityService $seasonCompatibility,
        private readonly StyleCompatibilityService $styleCompatibility,
    ) {
    }

    /**
     * @param ClothingItem[] $seedItems
     * @return OutfitSuggestion[]
     */
    public function suggest(
        array $seedItems,
        StyleType $style,
        SeasonType $season,
        ?WeatherConditionType $weather = null,
        int $count = 2
    ): array {
        // Step 1: Check seed items for style and season compatibility
        foreach ($seedItems as $seedItem) {
            if (!$this->isSeedItemCompatible($seedItem, $style, $season, $weather)) {
                return [];
            }
        }

        // Step 2: find missing zones
        $missingZones = $this->zoneResolver->resolveMissingZones($seedItems);

        // Step 3: Load candidates per zone from DB
        $candidatesByZone = $this->loadCandidates(
            $missingZones,
            $seedItems,
            $style,
            $season,
            $weather
        );

        //Step 4: Check if all required zones are covered by seed items or candidates
        if (!$this->canCoverRequiredZones($seedItems, $candidatesByZone)) {
            return [];
        }

        // Step 5: Build suggestions
        return $this->buildSuggestions($seedItems, $missingZones, $candidatesByZone, $count);
    }

    /**
     * Checks if the seed item is compatible with the given style, season and weather.
     */
    private function isSeedItemCompatible(
        ClothingItem $item,
        StyleType $style,
        SeasonType $season,
        ?WeatherConditionType $weather
    ): bool {
        // Season-Check
        if (!$this->seasonCompatibility->isCompatible($item, $season, $weather)) {
            return false;
        }

        // Style-Check: at least one style of the item must be compatible
        $itemStyles = $item->getStyles();
        if ($itemStyles->isEmpty()) {
            return true;
        }

        $requestedStyle = new Style();
        $requestedStyle->setType($style);

        foreach ($itemStyles as $itemStyle) {
            if ($this->styleCompatibility->areCompatible($itemStyle, $requestedStyle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Load compatible candidates per zone from DB.
     *
     * @param BodyZone[]     $zones
     * @param ClothingItem[] $seedItems
     * @return array<string, list<ClothingItem>>
     */
    private function loadCandidates(
        array $zones,
        array $seedItems,
        StyleType $style,
        SeasonType $season,
        ?WeatherConditionType $weather
    ): array {
        $candidates = [];

        foreach ($zones as $zone) {
            /** @var array<int, ClothingItem> $zoneItems */
            $zoneItems = $this->clothingItemRepository->findByBodyZone($zone);

            $compatible = array_filter(
                $zoneItems,
                fn(ClothingItem $candidate) => $this->isCandidateCompatible(
                    $candidate,
                    $seedItems,
                    $style,
                    $season,
                    $weather
                )
            );

            $candidates[$zone->value] = array_values($compatible);
        }

        return $candidates;
    }

    /**
     * Checks if the candidate is compatible with all seed items and filters.
     *
     * @param ClothingItem[] $seedItems
     */
    private function isCandidateCompatible(
        ClothingItem $candidate,
        array $seedItems,
        StyleType $style,
        SeasonType $season,
        ?WeatherConditionType $weather
    ): bool {
        // Season + Wetter
        if (!$this->seasonCompatibility->isCompatible($candidate, $season, $weather)) {
            return false;
        }

        // Style
        $requestedStyle = new Style();
        $requestedStyle->setType($style);

        $candidateStyles = $candidate->getStyles();
        if (!$candidateStyles->isEmpty()) {
            $styleCompatible = false;
            foreach ($candidateStyles as $candidateStyle) {
                if ($this->styleCompatibility->areCompatible($candidateStyle, $requestedStyle)) {
                    $styleCompatible = true;
                    break;
                }
            }
            if (!$styleCompatible) {
                return false;
            }
        }

        // Checks Color + Pattern compatibility against all seed items
        foreach ($seedItems as $seedItem) {
            if (!$this->areItemsCompatible($candidate, $seedItem)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks color and pattern compatibility between two items.
     */
    private function areItemsCompatible(ClothingItem $a, ClothingItem $b): bool
    {
        $colorA = $this->getPrimaryColor($a);
        $colorB = $this->getPrimaryColor($b);

        if ($colorA !== null && $colorB !== null) {
            if (!$this->colorCompatibility->areCompatible($colorA, $colorB)) {
                return false;
            }
        }

        $patternA = $a->getPattern();
        $patternB = $b->getPattern();

        if (!$this->patternCompatibility->areCompatible($patternA, $patternB)) {
            return false;
        }

        return true;
    }

    /**
     * Gives the primary color of an item.
     */
    private function getPrimaryColor(ClothingItem $item): ?\App\Entity\Color
    {
        foreach ($item->getItemColors() as $itemColor) {
            if ($itemColor->isPrimary()) {
                return $itemColor->getColor();
            }
        }
        return null;
    }

    /**
     * Checks if all required zones are covered by seed items or candidates.
     *
     * @param ClothingItem[]             $seedItems
     * @param array<string, ClothingItem[]> $candidatesByZone
     */
    private function canCoverRequiredZones(array $seedItems, array $candidatesByZone): bool
    {
        $coveredBySeed = array_map(
            fn(ClothingItem $item) => $item->getSubCategory()?->getCategory()?->getBodyZone(),
            $seedItems
        );

        // FULL_BODY covers UPPER_BODY and LOWER_BODY
        $expandedCovered = [];
        foreach ($coveredBySeed as $zone) {
            if ($zone === BodyZone::FULL_BODY) {
                $expandedCovered[] = BodyZone::UPPER_BODY;
                $expandedCovered[] = BodyZone::LOWER_BODY;
            } else {
                $expandedCovered[] = $zone;
            }
        }

        foreach (self::REQUIRED_ZONES as $required) {
            if (in_array($required, $expandedCovered, true)) {
                continue;
            }
            if (empty($candidatesByZone[$required->value] ?? [])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Builds to $count different OutfitSuggestions.
     *
     * @param ClothingItem[]                $seedItems
     * @param BodyZone[]                    $missingZones
     * @param array<string, ClothingItem[]> $candidatesByZone
     * @return OutfitSuggestion[]
     */
    private function buildSuggestions(
        array $seedItems,
        array $missingZones,
        array $candidatesByZone,
        int $count
    ): array {
        $suggestions   = [];
        $usedCombinations = [];

        $attempts = 0;
        $maxAttempts = $count * 10;

        while (count($suggestions) < $count && $attempts < $maxAttempts) {
            $attempts++;

            $suggestion = new OutfitSuggestion();

            // Add seed items
            foreach ($seedItems as $seedItem) {
                $suggestion->addItem($seedItem);
            }

            $selectedForZones = [];
            $valid = true;

            // For each missing zone, pick a random candidate
            foreach ($missingZones as $zone) {
                $candidates = $candidatesByZone[$zone->value] ?? [];

                if (empty($candidates)) {
                    // Skip optional zone
                    if (!in_array($zone, self::REQUIRED_ZONES, true)) {
                        continue;
                    }
                    // Mandatory zone without candidates → invalid suggestion
                    $valid = false;
                    break;
                }

                // Choose a random candidate
                $candidate = $candidates[array_rand($candidates)];

                // Checks compatibility with already selected items
                foreach ($selectedForZones as $alreadySelected) {
                    if (!$this->areItemsCompatible($candidate, $alreadySelected)) {
                        $valid = false;
                        break 2;
                    }
                }

                $selectedForZones[] = $candidate;
                $suggestion->addItem($candidate);
            }

            if (!$valid) {
                continue;
            }

            // Calculate score
            $this->calculateScore($suggestion);

            // Check duplicates
            $ids = array_map(fn(ClothingItem $i) => $i->getId(), $suggestion->getItems());
            sort($ids);
            $key = implode('-', $ids);

            if (!in_array($key, $usedCombinations, true)) {
                $usedCombinations[] = $key;
                $suggestions[]      = $suggestion;
            }
        }

        return $suggestions;
    }

    /**
     * Calculate a simple compatibility score for an outfit suggestion.
     */
    private function calculateScore(OutfitSuggestion $suggestion): void
    {
        $items  = $suggestion->getItems();
        $score  = 0;
        $pairs  = 0;

        for ($i = 0; $i < count($items); $i++) {
            for ($j = $i + 1; $j < count($items); $j++) {
                $colorA = $this->getPrimaryColor($items[$i]);
                $colorB = $this->getPrimaryColor($items[$j]);

                if ($colorA !== null && $colorB !== null) {
                    if ($this->colorCompatibility->areCompatible($colorA, $colorB)) {
                        $score++;
                    }
                }
                $pairs++;
            }
        }

        $suggestion->setScore($pairs > 0 ? (int) round(($score / $pairs) * 100) : 0);
    }
}
