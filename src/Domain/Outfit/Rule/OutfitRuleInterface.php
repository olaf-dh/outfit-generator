<?php

declare(strict_types=1);

namespace App\Domain\Outfit\Rule;

use App\Domain\Outfit\DTO\OutfitSuggestion;
use App\Domain\Outfit\Generator\OutfitContext;

interface OutfitRuleInterface
{
    public function apply(
        OutfitSuggestion $suggestion,
        OutfitContext $context
    ): OutfitSuggestion;
}
