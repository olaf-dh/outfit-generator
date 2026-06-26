<?php

declare(strict_types=1);

namespace App\Tests\Unit\Color\Resolver;

use App\Color\Resolver\ColorFamilyResolver;
use App\DTO\Color\ExtractedColor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ColorFamilyResolverBoundaryTest extends TestCase
{
    /**
     * @param ExtractedColor $color
     * @param array<string> $expectedFamilies
     * @return void
     */
    #[DataProvider('boundaryColorProvider')]
    public function testResolvesBoundaryColors(
        ExtractedColor $color,
        array $expectedFamilies,
    ): void {
        $resolver = new ColorFamilyResolver();

        self::assertSame($expectedFamilies, $resolver->resolve($color));
    }

    public static function boundaryColorProvider(): iterable
    {
        yield 'orange red' => [
            'color' => new ExtractedColor(
                hex: '#FF4500',
                r: 255,
                g: 69,
                b: 0,
                h: 16,
                s: 100,
                v: 100,
                weight: 1.0,
            ),
            'expectedFamilies' => ['red', 'orange'],
        ];

        yield 'tomato' => [
            'color' => new ExtractedColor(
                hex: '#FF6347',
                r: 255,
                g: 99,
                b: 71,
                h: 9,
                s: 72,
                v: 100,
                weight: 1.0,
            ),
            'expectedFamilies' => ['red', 'orange'],
        ];

        yield 'flame' => [
            'color' => new ExtractedColor(
                hex: '#E25822',
                r: 226,
                g: 88,
                b: 34,
                h: 17,
                s: 85,
                v: 89,
                weight: 1.0,
            ),
            'expectedFamilies' => ['red', 'orange'],
        ];

        yield 'copper' => [
            'color' => new ExtractedColor(
                hex: '#B87333',
                r: 184,
                g: 115,
                b: 51,
                h: 29,
                s: 72,
                v: 72,
                weight: 1.0,
            ),
            'expectedFamilies' => ['brown', 'orange', 'yellow'],
        ];

        yield 'rust' => [
            'color' => new ExtractedColor(
                hex: '#B7410E',
                r: 183,
                g: 65,
                b: 14,
                h: 18,
                s: 92,
                v: 72,
                weight: 1.0,
            ),
            'expectedFamilies' => ['brown', 'orange'],
        ];

        yield 'ochre' => [
            'color' => new ExtractedColor(
                hex: '#CC7722',
                r: 204,
                g: 119,
                b: 34,
                h: 30,
                s: 83,
                v: 80,
                weight: 1.0,
            ),
            'expectedFamilies' => ['brown', 'orange', 'yellow'],
        ];

        yield 'chocolate' => [
            'color' => new ExtractedColor(
                hex: '#D2691E',
                r: 210,
                g: 105,
                b: 30,
                h: 25,
                s: 86,
                v: 82,
                weight: 1.0,
            ),
            'expectedFamilies' => ['brown', 'orange', 'yellow'],
        ];

        yield 'khaki brown' => [
            'color' => new ExtractedColor(
                hex: '#C4A882',
                r: 196,
                g: 168,
                b: 130,
                h: 35,
                s: 34,
                v: 77,
                weight: 1.0,
            ),
            'expectedFamilies' => ['beige', 'brown', 'orange', 'yellow'],
        ];

        yield 'tan' => [
            'color' => new ExtractedColor(
                hex: '#D2B48C',
                r: 210,
                g: 180,
                b: 140,
                h: 34,
                s: 33,
                v: 82,
                weight: 1.0,
            ),
            'expectedFamilies' => ['beige', 'brown', 'orange', 'yellow'],
        ];

        yield 'pale taupe' => [
            'color' => new ExtractedColor(
                hex: '#BC987E',
                r: 188,
                g: 152,
                b: 126,
                h: 25,
                s: 33,
                v: 74,
                weight: 1.0,
            ),
            'expectedFamilies' => ['gray', 'beige', 'brown', 'orange', 'yellow'],
        ];

        yield 'cream' => [
            'color' => new ExtractedColor(
                hex: '#FFFDD0',
                r: 255,
                g: 253,
                b: 208,
                h: 57,
                s: 18,
                v: 100,
                weight: 1.0,
            ),
            'expectedFamilies' => ['beige'],
        ];

        yield 'ivory' => [
            'color' => new ExtractedColor(
                hex: '#FFFFF0',
                r: 255,
                g: 255,
                b: 240,
                h: 60,
                s: 6,
                v: 100,
                weight: 1.0,
            ),
            'expectedFamilies' => ['white', 'beige'],
        ];

        yield 'linen' => [
            'color' => new ExtractedColor(
                hex: '#FAF0E6',
                r: 250,
                g: 240,
                b: 230,
                h: 30,
                s: 8,
                v: 98,
                weight: 1.0,
            ),
            'expectedFamilies' => ['white', 'beige'],
        ];

        yield 'wheat' => [
            'color' => new ExtractedColor(
                hex: '#F5DEB3',
                r: 245,
                g: 222,
                b: 179,
                h: 39,
                s: 27,
                v: 96,
                weight: 1.0,
            ),
            'expectedFamilies' => ['beige', 'orange', 'yellow'],
        ];

        yield 'pale goldenrod' => [
            'color' => new ExtractedColor(
                hex: '#EEE8AA',
                r: 238,
                g: 232,
                b: 170,
                h: 55,
                s: 29,
                v: 93,
                weight: 1.0,
            ),
            'expectedFamilies' => ['beige', 'yellow'],
        ];

        yield 'crimson' => [
            'color' => new ExtractedColor(
                hex: '#DC143C',
                r: 220,
                g: 20,
                b: 60,
                h: 348,
                s: 91,
                v: 86,
                weight: 1.0,
            ),
            'expectedFamilies' => ['pink', 'red'],
        ];

        yield 'ruby' => [
            'color' => new ExtractedColor(
                hex: '#E0115F',
                r: 224,
                g: 17,
                b: 95,
                h: 337,
                s: 92,
                v: 88,
                weight: 1.0,
            ),
            'expectedFamilies' => ['pink'],
        ];

        yield 'medium violet red' => [
            'color' => new ExtractedColor(
                hex: '#C71585',
                r: 199,
                g: 21,
                b: 133,
                h: 322,
                s: 89,
                v: 78,
                weight: 1.0,
            ),
            'expectedFamilies' => ['pink', 'purple'],
        ];

        yield 'fuchsia' => [
            'color' => new ExtractedColor(
                hex: '#FF00FF',
                r: 255,
                g: 0,
                b: 255,
                h: 300,
                s: 100,
                v: 100,
                weight: 1.0,
            ),
            'expectedFamilies' => ['purple'],
        ];

        yield 'orchid' => [
            'color' => new ExtractedColor(
                hex: '#DA70D6',
                r: 218,
                g: 112,
                b: 214,
                h: 302,
                s: 49,
                v: 85,
                weight: 1.0,
            ),
            'expectedFamilies' => ['purple'],
        ];

        yield 'violet' => [
            'color' => new ExtractedColor(
                hex: '#EE82EE',
                r: 238,
                g: 130,
                b: 238,
                h: 300,
                s: 45,
                v: 93,
                weight: 1.0,
            ),
            'expectedFamilies' => ['purple'],
        ];

        yield 'indigo' => [
            'color' => new ExtractedColor(
                hex: '#4B0082',
                r: 75,
                g: 0,
                b: 130,
                h: 275,
                s: 100,
                v: 51,
                weight: 1.0,
            ),
            'expectedFamilies' => ['blue', 'purple'],
        ];

        yield 'blue violet' => [
            'color' => new ExtractedColor(
                hex: '#8A2BE2',
                r: 138,
                g: 43,
                b: 226,
                h: 271,
                s: 81,
                v: 89,
                weight: 1.0,
            ),
            'expectedFamilies' => ['blue', 'purple'],
        ];

        yield 'slate' => [
            'color' => new ExtractedColor(
                hex: '#708090',
                r: 112,
                g: 128,
                b: 144,
                h: 210,
                s: 22,
                v: 56,
                weight: 1.0,
            ),
            'expectedFamilies' => ['gray', 'blue'],
        ];

        yield 'light steel blue' => [
            'color' => new ExtractedColor(
                hex: '#B0C4DE',
                r: 176,
                g: 196,
                b: 222,
                h: 214,
                s: 27,
                v: 87,
                weight: 1.0,
            ),
            'expectedFamilies' => ['blue'],
        ];

        yield 'charcoal' => [
            'color' => new ExtractedColor(
                hex: '#36454F',
                r: 54,
                g: 69,
                b: 79,
                h: 204,
                s: 32,
                v: 31,
                weight: 1.0,
            ),
            'expectedFamilies' => ['gray'],
        ];

        yield 'dark slate gray' => [
            'color' => new ExtractedColor(
                hex: '#2F4F4F',
                r: 47,
                g: 79,
                b: 79,
                h: 180,
                s: 41,
                v: 31,
                weight: 1.0,
            ),
            'expectedFamilies' => ['gray', 'green'],
        ];

        yield 'yellow green' => [
            'color' => new ExtractedColor(
                hex: '#9ACD32',
                r: 154,
                g: 205,
                b: 50,
                h: 80,
                s: 76,
                v: 80,
                weight: 1.0,
            ),
            'expectedFamilies' => ['yellow', 'green'],
        ];

        yield 'green yellow' => [
            'color' => new ExtractedColor(
                hex: '#ADFF2F',
                r: 173,
                g: 255,
                b: 47,
                h: 84,
                s: 82,
                v: 100,
                weight: 1.0,
            ),
            'expectedFamilies' => ['yellow', 'green'],
        ];

        yield 'dark khaki' => [
            'color' => new ExtractedColor(
                hex: '#BDB76B',
                r: 189,
                g: 183,
                b: 107,
                h: 56,
                s: 43,
                v: 74,
                weight: 1.0,
            ),
            'expectedFamilies' => ['gray', 'beige', 'yellow'],
        ];

        yield 'olive' => [
            'color' => new ExtractedColor(
                hex: '#808000',
                r: 128,
                g: 128,
                b: 0,
                h: 60,
                s: 100,
                v: 50,
                weight: 1.0,
            ),
            'expectedFamilies' => ['yellow', 'green'],
        ];

        yield 'olive drab' => [
            'color' => new ExtractedColor(
                hex: '#6B8E23',
                r: 107,
                g: 142,
                b: 35,
                h: 80,
                s: 75,
                v: 56,
                weight: 1.0,
            ),
            'expectedFamilies' => ['yellow', 'green'],
        ];

        yield 'chartreuse' => [
            'color' => new ExtractedColor(
                hex: '#EAE000',
                r: 234,
                g: 224,
                b: 0,
                h: 57,
                s: 100,
                v: 92,
                weight: 1.0,
            ),
            'expectedFamilies' => ['yellow'],
        ];
    }
}
