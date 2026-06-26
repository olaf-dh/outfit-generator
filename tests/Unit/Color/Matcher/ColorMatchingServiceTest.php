<?php

declare(strict_types=1);

namespace App\Tests\Unit\Color\Matcher;

use App\Color\Enum\ColorFamily;
use App\Color\Enum\ColorSaturation;
use App\Color\Enum\ColorTemperature;
use App\Color\Enum\ColorTone;
use App\Color\Matcher\ColorDistanceService;
use App\Color\Matcher\ColorMatchingService;
use App\Color\Service\ColorConverterService;
use App\DTO\Color\HsvColor;
use App\DTO\Color\RgbColor;
use App\Entity\Color;
use DomainException;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
final class ColorMatchingServiceTest extends TestCase
{
    private ColorMatchingService $service;

    protected function setUp(): void
    {
        $colorDist = new ColorDistanceService(new ColorConverterService());

        $this->service = new ColorMatchingService($colorDist);
    }

    // --- Helper methods ---

    /**
     * @param string $name
     * @param string $hexCode
     * @param RgbColor $rgb
     * @param HsvColor $hsv
     * @return Color
     */
    private function makeColor(string $name, string $hexCode, RgbColor $rgb, HsvColor $hsv): Color
    {
        $color = new Color();
        $color->setName($name);
        $color->setHexCode($hexCode);
        $color->setFamily(ColorFamily::GRAY);
        $color->setTone(ColorTone::MEDIUM);
        $color->setTemperature(ColorTemperature::NEUTRAL);
        $color->setSaturation(ColorSaturation::MUTED);
        $color->setR($rgb->r ?? 0);
        $color->setG($rgb->g ?? 0);
        $color->setB($rgb->b ?? 0);
        $color->setH($hsv->h ?? 0);
        $color->setS($hsv->s ?? 0);
        $color->setV($hsv->v ?? 0);

        return $color;
    }

    // -------------------------------------------------------
    // Exact Match
    // -------------------------------------------------------
    public function testExactHexMatchReturnsCorrectColor(): void
    {
        $colors = [
            $this->makeColor(
                'Charcoal',
                '#36454F',
                new RgbColor(54, 69, 79),
                new HsvColor(204, 32, 31)
            ),
            $this->makeColor(
                'Mist',
                '#E3E3E3',
                new RgbColor(227, 227, 227),
                new HsvColor(0, 0, 89)
            ),
            $this->makeColor(
                'Navy',
                '#000080',
                new RgbColor(0, 0, 128),
                new HsvColor(240, 1, 50)
            ),
        ];

        $result = $this->service->findClosest('#36454F', $colors);
        $this->assertEquals('Charcoal', $result->getName());
    }

    // -------------------------------------------------------
    // Next color when similar hex code
    // -------------------------------------------------------
    public function testNearlyIdenticalHexReturnsClosestColor(): void
    {
        $colors = [
            $this->makeColor(
                'Slate',
                '#708090',
                new RgbColor(112, 128, 144),
                new HsvColor(210, 22, 56)
            ),
            $this->makeColor(
                'Dove Gray',
                '#D3D3D3',
                new RgbColor(211, 211, 211),
                new HsvColor(0, 0, 80)
            ),
            $this->makeColor(
                'Navy',
                '#000080',
                new RgbColor(0, 0, 128),
                new HsvColor(240, 1, 50)
            ),
        ];

        // #474A51 is very close to #708090
        $result = $this->service->findClosest('#474A51', $colors);
        $this->assertEquals('Slate', $result->getName());
    }

    public function testLightGrayIsCloserThanDarkGray(): void
    {
        $colors = [
            $this->makeColor(
                'Slate',
                '#708090',
                new RgbColor(112, 128, 144),
                new HsvColor(210, 22, 56)
            ),
            $this->makeColor(
                'Dove Gray',
                '#D3D3D3',
                new RgbColor(211, 211, 211),
                new HsvColor(0, 0, 80)
            ),
        ];

        // #E3E3E3 is very close to dove gray
        $result = $this->service->findClosest('#E3E3E3', $colors);
        $this->assertEquals('Dove Gray', $result->getName());
    }

    // -------------------------------------------------------
    // Empty color list
    // -------------------------------------------------------

    public function testEmptyColorListReturnsException(): void
    {
        $this->expectException(DomainException::class);

        $this->service->findClosest('#383838', []);
    }

    // -------------------------------------------------------
    // Single color in the list
    // -------------------------------------------------------
    public function testSingleColorIsAlwaysReturned(): void
    {
        $colors = [$this->makeColor(
            'Charcoal',
            '#36454F',
            new RgbColor(54, 69, 79),
            new HsvColor(204, 32, 31)
        )];

        $result = $this->service->findClosest('#FFFFFF', $colors);
        $this->assertEquals('Charcoal', $result->getName());
    }
}
