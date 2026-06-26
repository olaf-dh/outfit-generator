<?php

declare(strict_types=1);

namespace App\Tests\Unit\Outfit\Rule;

use App\Color\Enum\ColorFamily;
use App\Color\Enum\ColorSaturation;
use App\Color\Enum\ColorTemperature;
use App\Color\Enum\ColorTone;
use App\Entity\Color;
use App\Outfit\Rule\ColorCompatibilityService;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class ColorCompatibilityServiceTest extends TestCase
{
    private ColorCompatibilityService $service;

    protected function setUp(): void
    {
        $this->service = new ColorCompatibilityService();
    }

    // Helper method to build color-objects fast
    private function makeColor(
        ColorFamily $family,
        ColorTone $tone,
        ColorTemperature $temperature,
        ColorSaturation $saturation = ColorSaturation::MEDIUM
    ): Color {
        $color = new Color();
        $color->setFamily($family);
        $color->setTone($tone);
        $color->setTemperature($temperature);
        $color->setSaturation($saturation);

        return $color;
    }

    // -------------------------------------------------------
    // Neutral-Rule: Black, White, Gray, Brown, Beige
    // fit to any other color
    // -------------------------------------------------------

    public function testGrayIsCompatibleWithBrown(): void
    {
        $darkGray = $this->makeColor(ColorFamily::GRAY, ColorTone::MEDIUM, ColorTemperature::NEUTRAL);
        $cognac    = $this->makeColor(ColorFamily::BROWN, ColorTone::MEDIUM, ColorTemperature::WARM);

        $this->assertTrue($this->service->areCompatible($darkGray, $cognac));
    }

    public function testBlackIsCompatibleWithRed(): void
    {
        $black = $this->makeColor(ColorFamily::BLACK, ColorTone::DARK, ColorTemperature::NEUTRAL);
        $red   = $this->makeColor(ColorFamily::RED, ColorTone::MEDIUM, ColorTemperature::WARM);

        $this->assertTrue($this->service->areCompatible($black, $red));
    }

    public function testGrayIsCompatibleWithGreen(): void
    {
        $gray  = $this->makeColor(ColorFamily::GRAY, ColorTone::DARK, ColorTemperature::COOL);
        $olive = $this->makeColor(ColorFamily::GREEN, ColorTone::MEDIUM, ColorTemperature::WARM);

        $this->assertTrue($this->service->areCompatible($gray, $olive));
    }

    // -------------------------------------------------------
    // Same-Family-Rule:
    // Same Family + Different Tone = Harmonious
    // Same Family + Same Tone = Too Intense
    // -------------------------------------------------------

    public function testSameFamilyDifferentToneIsCompatible(): void
    {
        $lightGray = $this->makeColor(ColorFamily::GRAY, ColorTone::LIGHT, ColorTemperature::COOL);
        $darkGray = $this->makeColor(ColorFamily::GRAY, ColorTone::DARK, ColorTemperature::NEUTRAL);

        $this->assertTrue($this->service->areCompatible($lightGray, $darkGray));
    }

    public function testSameFamilySameToneIsNotCompatible(): void
    {
        $gray1 = $this->makeColor(ColorFamily::GRAY, ColorTone::DARK, ColorTemperature::COOL);
        $gray2 = $this->makeColor(ColorFamily::GRAY, ColorTone::DARK, ColorTemperature::COOL);

        $this->assertFalse($this->service->areCompatible($gray1, $gray2));
    }

    // -------------------------------------------------------
    // Temperature-Rule:
    // warm + warm = harmonious
    // cool + cool = harmonious
    // warm + cool = Contrast (only works with neutral tones)
    // -------------------------------------------------------

    public function testWarmColorsAreCompatible(): void
    {
        $cognac   = $this->makeColor(ColorFamily::BROWN, ColorTone::MEDIUM, ColorTemperature::WARM);
        $bordeaux = $this->makeColor(ColorFamily::RED, ColorTone::DARK, ColorTemperature::WARM);

        $this->assertTrue($this->service->areCompatible($cognac, $bordeaux));
    }

    public function testCoolColorsAreCompatible(): void
    {
        $blue    = $this->makeColor(ColorFamily::BLUE, ColorTone::DARK, ColorTemperature::COOL);
        $gray    = $this->makeColor(ColorFamily::GRAY, ColorTone::MEDIUM, ColorTemperature::COOL);

        $this->assertTrue($this->service->areCompatible($blue, $gray));
    }

    public function testBlueIsCompatibleWithBrown(): void
    {
        $cognac = $this->makeColor(ColorFamily::BROWN, ColorTone::MEDIUM, ColorTemperature::WARM);
        $blue   = $this->makeColor(ColorFamily::BLUE, ColorTone::DARK, ColorTemperature::COOL);

        $this->assertTrue($this->service->areCompatible($cognac, $blue));
    }

    // -------------------------------------------------------
    // Compatibility is symmetric:
    // A is compatible with B = B is compatible with A
    // -------------------------------------------------------

    public function testCompatibilityIsSymmetric(): void
    {
        $cognac = $this->makeColor(ColorFamily::BROWN, ColorTone::MEDIUM, ColorTemperature::WARM);
        $darkGray = $this->makeColor(ColorFamily::GRAY, ColorTone::DARK, ColorTemperature::NEUTRAL);

        $this->assertEquals(
            $this->service->areCompatible($cognac, $darkGray),
            $this->service->areCompatible($darkGray, $cognac)
        );
    }

    // -------------------------------------------------------
    // Saturation-Rule:
    // Muted + Muted = harmonious
    // Vivid + Vivid = loud → incompatible
    // Normal + Muted = harmonious
    // Normal + Vivid = acceptable
    // Normal + Normal = harmonious
    // Vivid + muted = Contrast (harmonious)
    // -------------------------------------------------------

    public function testMutedAndMutedIsCompatible(): void
    {
        $mutedRed  = $this->makeColor(
            ColorFamily::RED,
            ColorTone::MEDIUM,
            ColorTemperature::WARM,
            ColorSaturation::MUTED
        );
        $mutedBrown = $this->makeColor(
            ColorFamily::BROWN,
            ColorTone::MEDIUM,
            ColorTemperature::COOL,
            ColorSaturation::MUTED
        );

        $this->assertTrue($this->service->areCompatible($mutedRed, $mutedBrown));
    }

    public function testMutedAndNormalIsCompatible(): void
    {
        $mutedRed    = $this->makeColor(
            ColorFamily::RED,
            ColorTone::MEDIUM,
            ColorTemperature::WARM,
            ColorSaturation::MUTED
        );
        $normalBrown = $this->makeColor(
            ColorFamily::BROWN,
            ColorTone::MEDIUM,
            ColorTemperature::WARM
        );

        $this->assertTrue($this->service->areCompatible($mutedRed, $normalBrown));
    }

    public function testVividAndMutedIsCompatible(): void
    {
        $vividRed  = $this->makeColor(
            ColorFamily::RED,
            ColorTone::MEDIUM,
            ColorTemperature::WARM,
            ColorSaturation::VIBRANT
        );
        $mutedGray = $this->makeColor(
            ColorFamily::GRAY,
            ColorTone::MEDIUM,
            ColorTemperature::COOL,
            ColorSaturation::MUTED
        );

        $this->assertTrue($this->service->areCompatible($vividRed, $mutedGray));
    }

    public function testVividAndNormalIsCompatible(): void
    {
        $vividRed    = $this->makeColor(
            ColorFamily::RED,
            ColorTone::MEDIUM,
            ColorTemperature::WARM,
            ColorSaturation::VIBRANT
        );
        $normalBrown = $this->makeColor(
            ColorFamily::BROWN,
            ColorTone::MEDIUM,
            ColorTemperature::WARM
        );

        $this->assertTrue($this->service->areCompatible($vividRed, $normalBrown));
    }

    public function testVividAndVividIsNotCompatible(): void
    {
        $vividRed  = $this->makeColor(
            ColorFamily::RED,
            ColorTone::MEDIUM,
            ColorTemperature::WARM,
            ColorSaturation::VIBRANT
        );
        $vividBlue = $this->makeColor(
            ColorFamily::BLUE,
            ColorTone::MEDIUM,
            ColorTemperature::COOL,
            ColorSaturation::VIBRANT
        );

        $this->assertFalse($this->service->areCompatible($vividRed, $vividBlue));
    }

    public function testTwoVividNeutralsAreStillIncompatible(): void
    {
        $vividWhite = $this->makeColor(
            ColorFamily::WHITE,
            ColorTone::LIGHT,
            ColorTemperature::NEUTRAL,
            ColorSaturation::VIBRANT
        );
        $vividBlue  = $this->makeColor(
            ColorFamily::BLUE,
            ColorTone::MEDIUM,
            ColorTemperature::COOL,
            ColorSaturation::VIBRANT
        );

        $this->assertFalse($this->service->areCompatible($vividWhite, $vividBlue));
    }
}
