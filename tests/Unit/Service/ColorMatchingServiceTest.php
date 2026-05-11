<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Domain\Outfit\Enum\ColorFamily;
use App\Domain\Outfit\Enum\ColorSaturation;
use App\Domain\Outfit\Enum\ColorTemperature;
use App\Domain\Outfit\Enum\ColorTone;
use App\Entity\Color;
use App\Service\ColorMatchingService;
use PHPUnit\Framework\TestCase;

class ColorMatchingServiceTest extends TestCase
{
    private ColorMatchingService $service;

    protected function setUp(): void
    {
        $this->service = new ColorMatchingService();
    }

    // --- Helper method ---

    private function makeColor(string $name, string $hexCode): Color
    {
        $color = new Color();
        $color->setName($name);
        $color->setHexCode($hexCode);
        $color->setFamily(ColorFamily::GRAY);
        $color->setTone(ColorTone::MEDIUM);
        $color->setTemperature(ColorTemperature::NEUTRAL);
        $color->setSaturation(ColorSaturation::NORMAL);
        return $color;
    }

    // -------------------------------------------------------
    // Exact Match
    // -------------------------------------------------------

    public function testExactHexMatchReturnsCorrectColor(): void
    {
        $colors = [
            $this->makeColor('Charcoal', '#383838'),
            $this->makeColor('Light Gray', '#C8C8C8'),
            $this->makeColor('Navy', '#1B2A4A'),
        ];

        $result = $this->service->findClosest('#383838', $colors);

        $this->assertNotNull($result);
        $this->assertEquals('Charcoal', $result->getName());
    }

    // -------------------------------------------------------
    // Next color when similar hex code
    // -------------------------------------------------------

    public function testNearlyIdenticalHexReturnsClosestColor(): void
    {
        $colors = [
            $this->makeColor('Charcoal', '#383838'),
            $this->makeColor('Light Gray', '#C8C8C8'),
            $this->makeColor('Navy', '#1B2A4A'),
        ];

        // #3A3A3A is very close to #383838
        $result = $this->service->findClosest('#3A3A3A', $colors);

        $this->assertNotNull($result);
        $this->assertEquals('Charcoal', $result->getName());
    }

    public function testLightGrayIsCloserThanDarkGray(): void
    {
        $colors = [
            $this->makeColor('Charcoal', '#2F2F2F'),
            $this->makeColor('Light Gray', '#D0D0D0'),
        ];

        // #C8C8C8 is very close to light gray
        $result = $this->service->findClosest('#C8C8C8', $colors);

        $this->assertNotNull($result);
        $this->assertEquals('Light Gray', $result->getName());
    }

    // -------------------------------------------------------
    // Empty color list
    // -------------------------------------------------------

    public function testEmptyColorListReturnsNull(): void
    {
        $result = $this->service->findClosest('#383838', []);

        $this->assertNull($result);
    }

    // -------------------------------------------------------
    // Single color in the list
    // -------------------------------------------------------

    public function testSingleColorIsAlwaysReturned(): void
    {
        $colors = [$this->makeColor('Charcoal', '#383838')];

        $result = $this->service->findClosest('#FFFFFF', $colors);

        $this->assertNotNull($result);
        $this->assertEquals('Charcoal', $result->getName());
    }

    // -------------------------------------------------------
    // Color distance threshold
    // -------------------------------------------------------

    public function testFindClosestWithinThresholdReturnsColor(): void
    {
        $colors = [$this->makeColor('Charcoal', '#383838')];

        // #3A3A3A is very close to #383838 - under every reasonable threshold
        $result = $this->service->findClosestWithinThreshold('#3A3A3A', $colors, threshold: 10.0);

        $this->assertNotNull($result);
    }

    public function testFindClosestBeyondThresholdReturnsNull(): void
    {
        $colors = [$this->makeColor('Charcoal', '#383838')];

        // #FFFFFF (White) is very far away from Charcoal
        $result = $this->service->findClosestWithinThreshold('#FFFFFF', $colors, threshold: 10.0);

        $this->assertNull($result);
    }

    // -------------------------------------------------------
    // Symmetrical: A→B and B→A have the same distance
    // -------------------------------------------------------

    public function testColorDistanceIsSymmetric(): void
    {
        $colors1 = [$this->makeColor('Light Gray', '#C8C8C8')];
        $colors2 = [$this->makeColor('Charcoal', '#383838')];

        $distance1 = $this->service->calculateDistance('#383838', '#C8C8C8');
        $distance2 = $this->service->calculateDistance('#C8C8C8', '#383838');

        $this->assertEqualsWithDelta($distance1, $distance2, 0.001);
    }

    // -------------------------------------------------------
    // Identical colors have distance 0
    // -------------------------------------------------------

    public function testIdenticalColorsHaveZeroDistance(): void
    {
        $distance = $this->service->calculateDistance('#383838', '#383838');

        $this->assertEqualsWithDelta(0.0, $distance, 0.001);
    }
}
