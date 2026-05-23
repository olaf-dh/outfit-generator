<?php

declare(strict_types=1);

namespace App\Outfit\Rule;

use App\DTO\Outfit\OutfitSuggestion;
use App\Outfit\Generator\OutfitContext;

interface OutfitRuleInterface
{
    public function apply(
        OutfitSuggestion $suggestion,
        OutfitContext $context
    ): OutfitSuggestion;
}
