<?php

namespace App\Tests\Unit\Service;

use App\Entity\Pattern;
use App\Enum\PatternType;
use App\Service\PatternCompatibilityService;
use PHPUnit\Framework\TestCase;

class PatternCompatibilityServiceTest extends TestCase
{
    private PatternCompatibilityService $service;

    protected function setUp(): void
    {
        $this->service = new PatternCompatibilityService();
    }

    // --- Helper method ---

    private function makePattern(PatternType $type): Pattern
    {
        $pattern = new Pattern();
        $pattern->setType($type);
        return $pattern;
    }

    // -------------------------------------------------------
    // Neutral rules: everything is compatible with solid
    // -------------------------------------------------------

    public function testSolidIsCompatibleWithGeometric(): void
    {
        $solid   = $this->makePattern(PatternType::SOLID);
        $checked = $this->makePattern(PatternType::CHECKED);

        $this->assertTrue($this->service->areCompatible($solid, $checked));
    }

    public function testSolidIsCompatibleWithOrganic(): void
    {
        $solid  = $this->makePattern(PatternType::SOLID);
        $floral = $this->makePattern(PatternType::FLORAL);

        $this->assertTrue($this->service->areCompatible($solid, $floral));
    }

    public function testSolidIsCompatibleWithStatement(): void
    {
        $solid   = $this->makePattern(PatternType::SOLID);
        $novelty = $this->makePattern(PatternType::NOVELTY);

        $this->assertTrue($this->service->areCompatible($solid, $novelty));
    }

    public function testSolidIsCompatibleWithSolid(): void
    {
        $solid1 = $this->makePattern(PatternType::SOLID);
        $solid2 = $this->makePattern(PatternType::SOLID);

        $this->assertTrue($this->service->areCompatible($solid1, $solid2));
    }

    // -------------------------------------------------------
    // Geometric rules: two geometric patterns clash
    // -------------------------------------------------------

    public function testTwoCheckedPatternsAreNotCompatible(): void
    {
        $checked1 = $this->makePattern(PatternType::CHECKED);
        $checked2 = $this->makePattern(PatternType::CHECKED);

        $this->assertFalse($this->service->areCompatible($checked1, $checked2));
    }

    public function testCheckedAndDottedAreNotCompatible(): void
    {
        $checked = $this->makePattern(PatternType::CHECKED);
        $dotted  = $this->makePattern(PatternType::DOTTED);

        $this->assertFalse($this->service->areCompatible($checked, $dotted));
    }

    public function testVerticalAndHorizontalStripesAreNotCompatible(): void
    {
        $vertical   = $this->makePattern(PatternType::VERTICAL_STRIPES);
        $horizontal = $this->makePattern(PatternType::HORIZONTAL_STRIPES);

        $this->assertFalse($this->service->areCompatible($vertical, $horizontal));
    }

    public function testTwoVerticalStripesAreNotCompatible(): void
    {
        $vertical1 = $this->makePattern(PatternType::VERTICAL_STRIPES);
        $vertical2 = $this->makePattern(PatternType::VERTICAL_STRIPES);

        $this->assertFalse($this->service->areCompatible($vertical1, $vertical2));
    }

    // -------------------------------------------------------
    // Geometric + Organic = not compatible
    // -------------------------------------------------------

    public function testStripesAndFloralAreNotCompatible(): void
    {
        $stripes = $this->makePattern(PatternType::VERTICAL_STRIPES);
        $floral  = $this->makePattern(PatternType::FLORAL);

        $this->assertFalse($this->service->areCompatible($stripes, $floral));
    }

    public function testCheckedAndLeafAreNotCompatible(): void
    {
        $checked = $this->makePattern(PatternType::CHECKED);
        $leaf    = $this->makePattern(PatternType::LEAF);

        $this->assertFalse($this->service->areCompatible($checked, $leaf));
    }

    // -------------------------------------------------------
    // Organic + Organic = not compatible
    // -------------------------------------------------------

    public function testFloralAndLeafAreCompatible(): void
    {
        $floral = $this->makePattern(PatternType::FLORAL);
        $leaf   = $this->makePattern(PatternType::LEAF);

        $this->assertTrue($this->service->areCompatible($floral, $leaf));
    }

    // -------------------------------------------------------
    // Statement rules: only compatible with solid
    // -------------------------------------------------------

    public function testNoveltyWithSolidIsCompatible(): void
    {
        $novelty = $this->makePattern(PatternType::NOVELTY);
        $solid   = $this->makePattern(PatternType::SOLID);

        $this->assertTrue($this->service->areCompatible($novelty, $solid));
    }

    public function testNoveltyWithGeometricIsNotCompatible(): void
    {
        $novelty = $this->makePattern(PatternType::NOVELTY);
        $checked = $this->makePattern(PatternType::CHECKED);

        $this->assertFalse($this->service->areCompatible($novelty, $checked));
    }

    public function testNoveltyWithOrganicIsNotCompatible(): void
    {
        $novelty = $this->makePattern(PatternType::NOVELTY);
        $floral  = $this->makePattern(PatternType::FLORAL);

        $this->assertFalse($this->service->areCompatible($novelty, $floral));
    }

    public function testTwoStatementPatternsAreNotCompatible(): void
    {
        $novelty = $this->makePattern(PatternType::NOVELTY);
        $print   = $this->makePattern(PatternType::PRINT);

        $this->assertFalse($this->service->areCompatible($novelty, $print));
    }

    public function testMulticolorWithSolidIsCompatible(): void
    {
        $multicolor = $this->makePattern(PatternType::MULTICOLOR);
        $solid      = $this->makePattern(PatternType::SOLID);

        $this->assertTrue($this->service->areCompatible($multicolor, $solid));
    }

    public function testMulticolorWithGeometricIsNotCompatible(): void
    {
        $multicolor = $this->makePattern(PatternType::MULTICOLOR);
        $stripes    = $this->makePattern(PatternType::VERTICAL_STRIPES);

        $this->assertFalse($this->service->areCompatible($multicolor, $stripes));
    }

    // -------------------------------------------------------
    // Symmetric rules:
    //  - Pattern A is compatible with Pattern B if Pattern B is compatible with Pattern A
    //  - Pattern A is compatible with Pattern B if Pattern A is compatible with Pattern B
    // -------------------------------------------------------

    public function testCompatibilityIsSymmetric(): void
    {
        $novelty = $this->makePattern(PatternType::NOVELTY);
        $checked = $this->makePattern(PatternType::CHECKED);

        $this->assertEquals(
            $this->service->areCompatible($novelty, $checked),
            $this->service->areCompatible($checked, $novelty)
        );
    }
}
