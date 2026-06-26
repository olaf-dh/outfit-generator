<?php

declare(strict_types=1);

namespace App\Tests\Unit\Color\Service;

use App\Color\Service\ColorConverterService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
final class ColorConverterServiceTest extends TestCase
{
    private ColorConverterService $service;

    protected function setUp(): void
    {
        $this->service = new ColorConverterService();
    }

    public function testHexToRgbBlack(): void
    {
        $rgb = $this->service->hexToRgb('#000000');

        self::assertSame(0, $rgb->r);
        self::assertSame(0, $rgb->g);
        self::assertSame(0, $rgb->b);
    }

    public function testHexToRgbWhite(): void
    {
        $rgb = $this->service->hexToRgb('#FFFFFF');

        self::assertSame(255, $rgb->r);
        self::assertSame(255, $rgb->g);
        self::assertSame(255, $rgb->b);
    }

    public function testHexToRgbRed(): void
    {
        $rgb = $this->service->hexToRgb('#FF0000');

        self::assertSame(255, $rgb->r);
        self::assertSame(0, $rgb->g);
        self::assertSame(0, $rgb->b);
    }

    public function testHexToHsvBlack(): void
    {
        $hsv = $this->service->hexToHsv('#000000');

        self::assertSame(0, $hsv->h);
        self::assertSame(0, $hsv->s);
        self::assertSame(0, $hsv->v);
    }

    public function testHexToHsvWhite(): void
    {
        $hsv = $this->service->hexToHsv('#FFFFFF');

        self::assertSame(0, $hsv->s);
        self::assertSame(100, $hsv->v);
    }

    public function testHexToHsvRed(): void
    {
        $hsv = $this->service->hexToHsv('#FF0000');

        self::assertSame(0, $hsv->h);
        self::assertSame(100, $hsv->s);
        self::assertSame(100, $hsv->v);
    }

    public function testHexToHsvGreen(): void
    {
        $hsv = $this->service->hexToHsv('#00FF00');

        self::assertSame(120, $hsv->h);
    }

    public function testHexToHsvBlue(): void
    {
        $hsv = $this->service->hexToHsv('#0000FF');

        self::assertSame(240, $hsv->h);
    }

    public function testInvalidHexThrowsException(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->service->hexToRgb('abc');
    }

    public function testInvalidHexLengthThrowsException(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->service->hexToRgb('#12345');
    }
}
