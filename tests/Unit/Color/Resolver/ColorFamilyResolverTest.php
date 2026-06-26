<?php

declare(strict_types=1);

namespace App\Tests\Unit\Color\Resolver;

use App\Color\Resolver\ColorFamilyResolver;
use App\DTO\Color\ExtractedColor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
final class ColorFamilyResolverTest extends TestCase
{
    /**
     * @param ExtractedColor $color
     * @param string $expectedFamily
     * @return void
     */
    #[DataProvider('colorProvider')]
    public function testResolvesColorFamily(ExtractedColor $color, string $expectedFamily): void
    {
        $resolver = new ColorFamilyResolver();

        self::assertContains($expectedFamily, $resolver->resolve($color));
    }

    /**
     * @return iterable<string, array{
     *     color: ExtractedColor,
     *     expectedFamily: string
     * }>
     */
    public static function colorProvider(): iterable
    {
        yield 'black' => [
            'color' => new ExtractedColor(
                hex: '#000000',
                r: 0,
                g: 0,
                b: 0,
                h: 0,
                s: 0,
                v: 0,
                weight: 1.0
            ),
            'expectedFamily' => 'black',
        ];

        yield 'white' => [
            'color' => new ExtractedColor(
                hex: '#FFFFFF',
                r: 255,
                g: 255,
                b: 255,
                h: 0,
                s: 0,
                v: 100,
                weight: 1.0
            ),
            'expectedFamily' => 'white',
        ];

        yield 'gray' => [
            'color' => new ExtractedColor(
                hex: '#808080',
                r: 128,
                g: 128,
                b: 128,
                h: 0,
                s: 0,
                v: 50,
                weight: 1.0
            ),
            'expectedFamily' => 'gray',
        ];

        yield 'beige' => [
            'color' => new ExtractedColor(
                hex: '#F5F5DC',
                r: 245,
                g: 245,
                b: 220,
                h: 41,
                s: 26,
                v: 85,
                weight: 1.0
            ),
            'expectedFamily' => 'beige',
        ];

        yield 'brown' => [
            'color' => new ExtractedColor(
                hex: '#8B4513',
                r: 139,
                g: 69,
                b: 19,
                h: 25,
                s: 86,
                v: 55,
                weight: 1.0
            ),
            'expectedFamily' => 'brown',
        ];

        yield 'red' => [
            'color' => new ExtractedColor(
                hex: '#FF0000',
                r: 255,
                g: 0,
                b: 0,
                h: 0,
                s: 100,
                v: 100,
                weight: 1.0
            ),
            'expectedFamily' => 'red',
        ];

        yield 'pink' => [
            'color' => new ExtractedColor(
                hex: '#FF69B4',
                r: 255,
                g: 105,
                b: 180,
                h: 330,
                s: 59,
                v: 100,
                weight: 1.0
            ),
            'expectedFamily' => 'pink',
        ];

        yield 'orange' => [
            'color' => new ExtractedColor(
                hex: '#FFA500',
                r: 255,
                g: 165,
                b: 0,
                h: 39,
                s: 100,
                v: 100,
                weight: 1.0
            ),
            'expectedFamily' => 'orange',
        ];

        yield 'yellow' => [
            'color' => new ExtractedColor(
                hex: '#FFFF00',
                r: 255,
                g: 255,
                b: 0,
                h: 60,
                s: 100,
                v: 100,
                weight: 1.0
            ),
            'expectedFamily' => 'yellow',
        ];

        yield 'green' => [
            'color' => new ExtractedColor(
                hex: '#00FF00',
                r: 0,
                g: 255,
                b: 0,
                h: 120,
                s: 100,
                v: 100,
                weight: 1.0
            ),
            'expectedFamily' => 'green',
        ];

        yield 'blue' => [
            'color' => new ExtractedColor(
                hex: '#0000FF',
                r: 0,
                g: 0,
                b: 255,
                h: 240,
                s: 100,
                v: 100,
                weight: 1.0
            ),
            'expectedFamily' => 'blue',
        ];

        yield 'purple' => [
            'color' => new ExtractedColor(
                hex: '#800080',
                r: 128,
                g: 0,
                b: 128,
                h: 300,
                s: 100,
                v: 50,
                weight: 1.0
            ),
            'expectedFamily' => 'purple',
        ];
    }
}
