<?php

declare(strict_types=1);

namespace App\Color\Analyzer;

use App\Color\Service\ColorConverterService;
use App\DTO\Color\ExtractedColor;
use League\ColorExtractor\Color;
use League\ColorExtractor\Palette;

readonly class ColorExtractorService
{
    public function __construct(
        private ColorConverterService $converter,
    ) {
    }

    /**
     * Extrahiere die 10 häufigsten Farben ohne komplexe Filter
     *
     * @return array{
     *     primary: string,
     *     secondary: string[],
     *     all_colors: array<int, array{
     *         hex: string,
     *         weight: float,
     *         h: float,
     *         s: float,
     *         v: float
     *     }>
     * }
     */
    public function extract(string $imagePath): array
    {
        $palette = Palette::fromFilename($imagePath);
        $colors = $palette->getMostUsedColors(10);

        $result = [];
        /**
         * @var int $colorInt
         * @var float $weight
         */
        foreach ($colors as $colorInt => $weight) {
            $hex = Color::fromIntToHex($colorInt);
            $rgb = $this->converter->hexToRgb($hex);
            $hsv = $this->converter->hexToHsv($hex);

            $result[] = new ExtractedColor(
                hex: $hex,
                r: $rgb['r'],
                g: $rgb['g'],
                b: $rgb['b'],
                h: $hsv['h'],
                s: $hsv['s'],
                v: $hsv['v'],
                weight: $weight,
            );
        }

        // Sortiere nach Gewicht (absteigend)
        usort($result, fn($a, $b) => $b->weight <=> $a->weight);

        return [
            'primary' => $result[0]->hex ?? '#808080',
            'secondary' => array_slice(array_map(fn($c) => $c->hex, $result), 1, 2),
            'all_colors' => array_map(fn($c) => [
                'hex' => $c->hex,
                'weight' => $c->weight,
                'h' => $c->h,
                's' => $c->s,
                'v' => $c->v
            ], $result)
        ];
    }
}
