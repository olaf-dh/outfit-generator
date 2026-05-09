<?php

namespace App\Service;

use App\Domain\Outfit\Enum\BodyZone;
use App\Entity\ClothingItem;

class ZoneResolverService
{
    private const array REQUIRED_ZONES = [
        BodyZone::UPPER_BODY,
        BodyZone::LOWER_BODY,
        BodyZone::FOOTWEAR,
    ];

    private const array OPTIONAL_ZONES = [
        BodyZone::OUTER_LAYER,
        BodyZone::HEAD,
        BodyZone::ACCESSORY,
    ];

    /**
     * Determines which zones are missing from the given items.
     *
     * @param ClothingItem[] $items
     * @return BodyZone[]
     */
    public function resolveMissingZones(array $items): array
    {
        $coveredZones = $this->getCoveredZones($items);

        $requiredMissing = array_filter(
            self::REQUIRED_ZONES,
            fn(BodyZone $zone) => !in_array($zone, $coveredZones, true)
        );

        $optionalMissing = array_filter(
            self::OPTIONAL_ZONES,
            fn(BodyZone $zone) => !in_array($zone, $coveredZones, true)
        );

        return [...$requiredMissing, ...$optionalMissing];
    }

    /**
     * Returns all zones that are covered by the seed items.
     * FULL_BODY covers UPPER_BODY and LOWER_BODY at the same time.
     *
     * @param ClothingItem[] $seedItems
     * @return BodyZone[]
     */
    private function getCoveredZones(array $seedItems): array
    {
        $covered = [];

        foreach ($seedItems as $item) {
            $zone = $item->getSubCategory()?->getCategory()?->getBodyZone();

            if ($zone === null) {
                continue;
            }

            if ($zone === BodyZone::FULL_BODY) {
                $covered[] = BodyZone::UPPER_BODY;
                $covered[] = BodyZone::LOWER_BODY;
            } else {
                $covered[] = $zone;
            }
        }

        return $covered;
    }
}
