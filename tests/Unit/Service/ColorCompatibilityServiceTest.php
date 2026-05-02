<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Enum\ColorFamily;
use App\Enum\ColorSaturation;
use App\Enum\ColorTone;
use App\Enum\ColorTemperature;
use App\Service\ColorCompatibilityService;
use App\Entity\Color;
use PHPUnit\Framework\TestCase;

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
        ColorSaturation $saturation = ColorSaturation::NORMAL
    ): Color {
        $color = new Color();
        $color->setFamily($family);
        $color->setTone($tone);
        $color->setTemperature($temperature);
        $color->setSaturation($saturation);

        return $color;
    }

    // -------------------------------------------------------
    // Neutral-Rule: Black, White, Gray, Navy, Beige
    // fit to any other color
    // -------------------------------------------------------

    public function testGrayIsCompatibleWithBrown(): void
    {
        $anthracite = $this->makeColor(ColorFamily::GRAY, ColorTone::DARK, ColorTemperature::COOL);
        $cognac    = $this->makeColor(ColorFamily::BROWN, ColorTone::MEDIUM, ColorTemperature::WARM);

        $this->assertTrue($this->service->areCompatible($anthracite, $cognac));
    }

    public function testBlackIsCompatibleWithRed(): void
    {
        $black = $this->makeColor(ColorFamily::BLACK, ColorTone::DARK, ColorTemperature::NEUTRAL);
        $red   = $this->makeColor(ColorFamily::RED, ColorTone::MEDIUM, ColorTemperature::WARM);

        $this->assertTrue($this->service->areCompatible($black, $red));
    }

    public function testNavyIsCompatibleWithGreen(): void
    {
        $navy  = $this->makeColor(ColorFamily::NAVY, ColorTone::DARK, ColorTemperature::COOL);
        $olive = $this->makeColor(ColorFamily::GREEN, ColorTone::MEDIUM, ColorTemperature::WARM);

        $this->assertTrue($this->service->areCompatible($navy, $olive));
    }

    // -------------------------------------------------------
    // Same-Family-Rule:
    // Same Family + Different Tone = Harmonious
    // Same Family + Same Tone = Too Intense
    // -------------------------------------------------------

    public function testSameFamilyDifferentToneIsCompatible(): void
    {
        $lightGray  = $this->makeColor(ColorFamily::GRAY, ColorTone::LIGHT, ColorTemperature::COOL);
        $anthracite = $this->makeColor(ColorFamily::GRAY, ColorTone::DARK, ColorTemperature::COOL);

        $this->assertTrue($this->service->areCompatible($lightGray, $anthracite));
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
        $navy    = $this->makeColor(ColorFamily::NAVY, ColorTone::DARK, ColorTemperature::COOL);
        $gray    = $this->makeColor(ColorFamily::GRAY, ColorTone::MEDIUM, ColorTemperature::COOL);

        $this->assertTrue($this->service->areCompatible($navy, $gray));
    }

    public function testNavyIsCompatibleWithBrown(): void
    {
        $cognac = $this->makeColor(ColorFamily::BROWN, ColorTone::MEDIUM, ColorTemperature::WARM);
        $navy   = $this->makeColor(ColorFamily::NAVY, ColorTone::DARK, ColorTemperature::COOL);

        $this->assertTrue($this->service->areCompatible($cognac, $navy));
    }

    // -------------------------------------------------------
    // Compatibility is symmetric:
    // A is compatible with B = B is compatible with A
    // -------------------------------------------------------

    public function testCompatibilityIsSymmetric(): void
    {
        $cognac    = $this->makeColor(ColorFamily::BROWN, ColorTone::MEDIUM, ColorTemperature::WARM);
        $anthracite = $this->makeColor(ColorFamily::GRAY, ColorTone::DARK, ColorTemperature::COOL);

        $this->assertEquals(
            $this->service->areCompatible($cognac, $anthracite),
            $this->service->areCompatible($anthracite, $cognac)
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
        $mutedNavy = $this->makeColor(
            ColorFamily::NAVY,
            ColorTone::MEDIUM,
            ColorTemperature::COOL,
            ColorSaturation::MUTED
        );

        $this->assertTrue($this->service->areCompatible($mutedRed, $mutedNavy));
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
            ColorSaturation::VIVID
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
            ColorSaturation::VIVID
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
            ColorSaturation::VIVID
        );
        $vividNavy = $this->makeColor(
            ColorFamily::NAVY,
            ColorTone::MEDIUM,
            ColorTemperature::COOL,
            ColorSaturation::VIVID
        );

        $this->assertFalse($this->service->areCompatible($vividRed, $vividNavy));
    }

    public function testTwoVividNeutralsAreStillIncompatible(): void
    {
        $vividWhite = $this->makeColor(
            ColorFamily::WHITE,
            ColorTone::LIGHT,
            ColorTemperature::NEUTRAL,
            ColorSaturation::VIVID
        );
        $vividNavy  = $this->makeColor(
            ColorFamily::NAVY,
            ColorTone::MEDIUM,
            ColorTemperature::COOL,
            ColorSaturation::VIVID
        );

        $this->assertFalse($this->service->areCompatible($vividWhite, $vividNavy));
    }
}
