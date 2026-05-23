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
use App\DTO\Color\ExtractedColor;
use App\Entity\Color;
use App\Repository\ColorRepository;
use PHPUnit\Framework\TestCase;

class ColorMatchingServiceTest extends TestCase
{
    private ColorMatchingService $service;

    protected function setUp(): void
    {
        $colorRep = $this->createStub(ColorRepository::class);
        $colorDist = new ColorDistanceService(new ColorConverterService());

        $this->service = new ColorMatchingService($colorRep, $colorDist);
    }

    // --- Helper methods ---

    /**
     * @param string $name
     * @param string $hexCode
     * @param array<string, int> $rgb
     * @param array<string, float> $hsv
     * @return Color
     */
    private function makeColor(string $name, string $hexCode, array $rgb, array $hsv): Color
    {
        $color = new Color();
        $color->setName($name);
        $color->setHexCode($hexCode);
        $color->setFamily(ColorFamily::GRAY);
        $color->setTone(ColorTone::MEDIUM);
        $color->setTemperature(ColorTemperature::NEUTRAL);
        $color->setSaturation(ColorSaturation::MUTED);
        $color->setR($rgb['r'] ?? 0);
        $color->setG($rgb['g'] ?? 0);
        $color->setB($rgb['b'] ?? 0);
        $color->setH($hsv['h'] ?? 0);
        $color->setS($hsv['s'] ?? 0);
        $color->setV($hsv['v'] ?? 0);

        return $color;
    }

    /**
     * @param string $hex
     * @param array<string, int> $rgb
     * @param array<string, float> $hsv
     * @return ExtractedColor
     */
    private function makeExtractedColor(string $hex, array $rgb, array $hsv): ExtractedColor
    {
        return new ExtractedColor(
            hex: $hex,
            r: $rgb['r'],
            g: $rgb['g'],
            b: $rgb['b'],
            h: $hsv['h'],
            s: $hsv['s'],
            v: $hsv['v'],
            weight: (
                ($hsv['s'] * 0.75)
                + ($hsv['v'] * 0.25)
            )
        );
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
                ['r' => 54, 'g' => 69, 'b' => 79],
                ['h' => 204, 's' => 0.31645569620253, 'v' => 0.30980392156863]
            ),
            $this->makeColor(
                'Mist',
                '#E3E3E3',
                ['r' => 227, 'g' => 227, 'b' => 227],
                ['h' => 0, 's' => 0, 'v' => 0.89019607843137]
            ),
            $this->makeColor(
                'Navy',
                '#000080',
                ['r' => 0, 'g' => 0, 'b' => 128],
                ['h' => 240, 's' => 1, 'v' => 0.50196078431373]
            ),
        ];

        $result = $this->service->findClosest('#36454F', $colors);

        $this->assertNotNull($result);
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
                ['r' => 112, 'g' => 128, 'b' => 144],
                ['h' => 210, 's' => 0.22222222222222, 'v' => 0.56470588235294]
            ),
            $this->makeColor(
                'Dove Gray',
                '#D3D3D3',
                ['r' => 211, 'g' => 211, 'b' => 211],
                ['h' => 0, 's' => 0, 'v' => 0.80392156862745]
            ),
            $this->makeColor(
                'Navy',
                '#000080',
                ['r' => 0, 'g' => 0, 'b' => 128],
                ['h' => 240, 's' => 1, 'v' => 0.50196078431373]
            ),
        ];

        // #474A51 is very close to #708090
        $result = $this->service->findClosest('#474A51', $colors);

        $this->assertNotNull($result);
        $this->assertEquals('Slate', $result->getName());
    }

    public function testLightGrayIsCloserThanDarkGray(): void
    {
        $colors = [
            $this->makeColor(
                'Slate',
                '#708090',
                ['r' => 112, 'g' => 128, 'b' => 144],
                ['h' => 210, 's' => 0.22222222222222, 'v' => 0.56470588235294]
            ),
            $this->makeColor(
                'Dove Gray',
                '#D3D3D3',
                ['r' => 211, 'g' => 211, 'b' => 211],
                ['h' => 0, 's' => 0, 'v' => 0.80392156862745]
            ),
        ];

        // #E3E3E3 is very close to dove gray
        $result = $this->service->findClosest('#E3E3E3', $colors);

        $this->assertNotNull($result);
        $this->assertEquals('Dove Gray', $result->getName());
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
        $colors = [$this->makeColor(
            'Charcoal',
            '#36454F',
            ['r' => 54, 'g' => 69, 'b' => 79],
            ['h' => 204, 's' => 0.31645569620253, 'v' => 0.30980392156863]
        )];

        $result = $this->service->findClosest('#FFFFFF', $colors);

        $this->assertNotNull($result);
        $this->assertEquals('Charcoal', $result->getName());
    }

    // -------------------------------------------------------
    // Color distance threshold
    // -------------------------------------------------------
    public function testFindClosestWithinThresholdReturnsColor(): void
    {
        $colors = [$this->makeColor(
            'Charcoal',
            '#36454F',
            ['r' => 54, 'g' => 69, 'b' => 79],
            ['h' => 204, 's' => 0.31645569620253, 'v' => 0.30980392156863]
        )];

        // #3A3A3A is very close to #383838 - under every reasonable threshold
        $result = $this->service->findClosestWithinThreshold('#3A3A3A', $colors, threshold: 10.0);

        $this->assertNotNull($result);
    }

    public function testFindClosestBeyondThresholdReturnsNull(): void
    {
        $colors = [$this->makeColor(
            'Charcoal',
            '#36454F',
            ['r' => 54, 'g' => 69, 'b' => 79],
            ['h' => 204, 's' => 0.31645569620253, 'v' => 0.30980392156863]
        )];

        // #FFFFFF (White) is very far away from Charcoal
        $result = $this->service->findClosestWithinThreshold('#FFFFFF', $colors, threshold: 10.0);

        $this->assertNull($result);
    }

    // -------------------------------------------------------
    // Symmetrical: A→B and B→A have the same distance
    // -------------------------------------------------------
    public function testColorDistanceIsSymmetric(): void
    {
        $color1 = $this->makeColor(
            'Ash Gray',
            '#B2BEB5',
            ['r' => 178, 'g' => 190, 'b' => 181],
            ['h' => 135, 's' => 0.063157894736842, 'v' => 0.74509803921569]
        );
        $color2 = $this->makeColor(
            'Slate',
            '#708090',
            ['r' => 112, 'g' => 128, 'b' => 144],
            ['h' => 210, 's' => 0.22222222222222, 'v' => 0.56470588235294]
        );
        $ec1 = $this->makeExtractedColor(
            '#B2BEB5',
            ['r' => 178, 'g' => 190, 'b' => 181],
            ['h' => 135, 's' => 0.063157894736842, 'v' => 0.74509803921569]
        );
        $ec2 = $this->makeExtractedColor(
            '#708090',
            ['r' => 112, 'g' => 128, 'b' => 144],
            ['h' => 210, 's' => 0.22222222222222, 'v' => 0.56470588235294]
        );

        $distance1 = $this->service->calculateDistance($ec1, $color2);
        $distance2 = $this->service->calculateDistance($ec2, $color1);

        $this->assertEqualsWithDelta($distance1, $distance2, 0.001);
    }

    // -------------------------------------------------------
    // Identical colors have distance 0
    // -------------------------------------------------------
    public function testIdenticalColorsHaveZeroDistance(): void
    {
        $color = $this->makeColor(
            'Ash Gray',
            '#B2BEB5',
            ['r' => 178, 'g' => 190, 'b' => 181],
            ['h' => 135, 's' => 0.06, 'v' => 0.75]
        );
        $ec = $this->makeExtractedColor(
            '#B2BEB5',
            ['r' => 178, 'g' => 190, 'b' => 181],
            ['h' => 135, 's' => 0.06, 'v' => 0.75]
        );

        $distance = $this->service->calculateDistance($ec, $color);

        $this->assertEqualsWithDelta(0.0, $distance, 0.001);
    }
}
