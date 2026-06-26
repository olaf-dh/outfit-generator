<?php

declare(strict_types=1);

namespace App\Tests\Unit\Color\Matcher;

use App\Color\Matcher\ColorDistanceService;
use App\Color\Service\ColorConverterService;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
final class ColorDistanceServiceTest extends TestCase
{
    private ColorDistanceService $service;

    protected function setUp(): void
    {
        $colorConverterService = new ColorConverterService();
        $this->service = new ColorDistanceService($colorConverterService);
    }

    public function testIdenticalColorsReturnZeroDistance(): void
    {
        $distance = $this->service->deltaE('#ffffff', '#ffffff');

        self::assertSame(0.0, $distance);
    }

    public function testSimilarColorsReturnSmallDistance(): void
    {
        $distance = $this->service->deltaE('#ffffff', '#fefefe');

        self::assertLessThan(5.0, $distance);
    }

    public function testVeryDifferentColorsReturnLargeDistance(): void
    {
        $distance = $this->service->deltaE('#000000', '#ffffff');

        self::assertGreaterThan(50.0, $distance);
    }

    public function testRedVsBlueHasSignificantDistance(): void
    {
        $distance = $this->service->deltaE('#ff0000', '#0000ff');

        self::assertGreaterThan(30.0, $distance);
    }
}
