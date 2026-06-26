<?php

declare(strict_types=1);

namespace App\Tests\Unit\Outfit\Rule;

use App\ClothingItem\Enum\StyleType;
use App\Entity\Style;
use App\Outfit\Rule\StyleCompatibilityService;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class StyleCompatibilityServiceTest extends TestCase
{
    private StyleCompatibilityService $service;

    protected function setUp(): void
    {
        $this->service = new StyleCompatibilityService();
    }

    // --- Helper method ---

    private function makeStyle(StyleType $type): Style
    {
        $style = new Style();
        $style->setType($type);
        return $style;
    }

    // -------------------------------------------------------
    // Same style = always compatible
    // -------------------------------------------------------

    public function testCasualWithCasualIsCompatible(): void
    {
        $this->assertTrue($this->service->areCompatible(
            $this->makeStyle(StyleType::CASUAL),
            $this->makeStyle(StyleType::CASUAL)
        ));
    }

    public function testSmartCasualWithSmartCasualIsCompatible(): void
    {
        $this->assertTrue($this->service->areCompatible(
            $this->makeStyle(StyleType::SMART_CASUAL),
            $this->makeStyle(StyleType::SMART_CASUAL)
        ));
    }

    public function testBusinessWithBusinessIsCompatible(): void
    {
        $this->assertTrue($this->service->areCompatible(
            $this->makeStyle(StyleType::BUSINESS),
            $this->makeStyle(StyleType::BUSINESS)
        ));
    }

    // -------------------------------------------------------
    // Smart casual is the bridge style
    // -------------------------------------------------------

    public function testCasualWithSmartCasualIsCompatible(): void
    {
        $this->assertTrue($this->service->areCompatible(
            $this->makeStyle(StyleType::CASUAL),
            $this->makeStyle(StyleType::SMART_CASUAL)
        ));
    }

    public function testSmartCasualWithBusinessIsCompatible(): void
    {
        $this->assertTrue($this->service->areCompatible(
            $this->makeStyle(StyleType::SMART_CASUAL),
            $this->makeStyle(StyleType::BUSINESS)
        ));
    }

    // -------------------------------------------------------
    // Casual + Business = to far away
    // -------------------------------------------------------

    public function testCasualWithBusinessIsNotCompatible(): void
    {
        $this->assertFalse($this->service->areCompatible(
            $this->makeStyle(StyleType::CASUAL),
            $this->makeStyle(StyleType::BUSINESS)
        ));
    }

    // -------------------------------------------------------
    // Symmetric
    // -------------------------------------------------------

    public function testCompatibilityIsSymmetric(): void
    {
        $casual   = $this->makeStyle(StyleType::CASUAL);
        $business = $this->makeStyle(StyleType::BUSINESS);

        $this->assertEquals(
            $this->service->areCompatible($casual, $business),
            $this->service->areCompatible($business, $casual)
        );
    }

    public function testSmartCasualSymmetry(): void
    {
        $smartCasual = $this->makeStyle(StyleType::SMART_CASUAL);
        $casual      = $this->makeStyle(StyleType::CASUAL);

        $this->assertEquals(
            $this->service->areCompatible($smartCasual, $casual),
            $this->service->areCompatible($casual, $smartCasual)
        );
    }
}
