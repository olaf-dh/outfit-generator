<?php

declare(strict_types=1);

namespace App\Tests\Unit\Color\Analyzer;

use App\Color\Analyzer\ColorExtractorService;
use App\Color\Service\ColorConverterService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ColorExtractorServiceTest extends TestCase
{
    private ColorExtractorService $service;
    private ColorConverterService $converter;
    private string $testImagesDir;

    protected function setUp(): void
    {
        // Suppress deprecation warning of league/color-extractor
        set_error_handler(function (int $errno, string $errstr): bool {
            if (str_contains($errstr, 'imagedestroy')) {
                return true;
            }
            return false;
        });
        $this->converter = new ColorConverterService();

        $this->service = new ColorExtractorService($this->converter);
        $this->testImagesDir = sys_get_temp_dir() . '/color_extractor_test_' . uniqid();
        mkdir($this->testImagesDir, 0777, true);
    }

    protected function tearDown(): void
    {
        restore_error_handler();
        array_map('unlink', glob($this->testImagesDir . '/*') ?: []);
        rmdir($this->testImagesDir);
    }

    // --- Helper methods ---

    private function createSolidColorImage(string $path, int $r, int $g, int $b): void
    {
        $image = imagecreatetruecolor(100, 100);

        $r = $this->clampColor($r);
        $g = $this->clampColor($g);
        $b = $this->clampColor($b);

        $color = imagecolorallocate($image, $r, $g, $b);

        if ($color === false) {
            throw new RuntimeException('Could not allocate color.');
        }

        imagefill($image, 0, 0, $color);

        imagejpeg($image, $path, 90);
    }

    private function createGreenScreenImage(string $path): void
    {
        // Image with green screen background and red clothing item in the middle
        $image      = imagecreatetruecolor(200, 200);

        $r = $this->clampColor(0);
        $g = $this->clampColor(177);
        $b = $this->clampColor(64);

        $green = imagecolorallocate($image, $r, $g, $b); // typical Green-Screen

        if ($green === false) {
            throw new RuntimeException('Could not allocate color.');
        }

        $r = $this->clampColor(180);
        $g = $this->clampColor(30);
        $b = $this->clampColor(30);

        $red = imagecolorallocate($image, $r, $g, $b); // typical Green-Screen

        if ($red === false) {
            throw new RuntimeException('Could not allocate color.');
        }

        // Background is green
        imagefill($image, 0, 0, $green);

        // Clothing item in the middle is red
        imagefilledrectangle($image, 50, 50, 150, 150, $red);

        imagejpeg($image, $path, 90);
    }

    /**
     * @param int $value
     * @return int<0, 255>
     */
    private function clampColor(int $value): int
    {
        return max(0, min(255, $value));
    }

    // -------------------------------------------------------
    // Basic extraction
    // -------------------------------------------------------
    public function testExtractReturnsArray(): void
    {
        $path = $this->testImagesDir . '/test.jpg';
        $this->createSolidColorImage($path, 56, 56, 56); // Dark gray

        $result = $this->service->extract($path);

        $this->assertMatchesRegularExpression('/^#([0-9a-fA-F]{6})$/', $result['primary']);
    }

    public function testExtractReturnsPrimaryColor(): void
    {
        $path = $this->testImagesDir . '/test.jpg';
        $this->createSolidColorImage($path, 56, 56, 56);

        $result = $this->service->extract($path);

        $this->assertArrayHasKey('primary', $result);
        $this->assertMatchesRegularExpression('/^#([0-9a-fA-F]{6})$/', $result['primary']);
    }

    public function testExtractReturnsHexCode(): void
    {
        $path = $this->testImagesDir . '/test.jpg';
        $this->createSolidColorImage($path, 56, 56, 56);

        $result = $this->service->extract($path);

        $this->assertMatchesRegularExpression('/^#[0-9A-Fa-f]{6}$/', $result['primary']);
    }

    public function testExtractReturnsSecondaryColorsArray(): void
    {
        $path = $this->testImagesDir . '/test.jpg';
        $this->createSolidColorImage($path, 56, 56, 56);

        $result = $this->service->extract($path);

        $this->assertArrayHasKey('secondary', $result);
        $this->assertCount(0, $result['secondary']);
    }

    public function testExtractReturnsMaxTwoSecondaryColors(): void
    {
        $path = $this->testImagesDir . '/test.jpg';
        $this->createSolidColorImage($path, 56, 56, 56);

        $result = $this->service->extract($path);

        $this->assertLessThanOrEqual(2, count($result['secondary']));
    }

    // -------------------------------------------------------
    // Green-Screen filter
    // -------------------------------------------------------
    public function testGreenScreenColorIsFiltered(): void
    {
        $path = $this->testImagesDir . '/greenscreen.jpg';
        $this->createGreenScreenImage($path);

        $result = $this->service->extract($path);

        // Green-Screen green should not be the primary color
        $this->assertNotEquals('#00B140', $result['primary']);
    }

    // -------------------------------------------------------
    // Gray regression test
    // -------------------------------------------------------
    public function testLightBlueIsNotDetectedAsGray(): void
    {
        $path = $this->testImagesDir . '/lightblue.jpg';
        $this->createSolidColorImage($path, 170, 200, 255); // light blue

        $result = $this->service->extract($path);

        $this->assertNotEquals('#c2c2c2', $result['primary']);
        $this->assertNotEquals('#d0d0d0', $result['primary']);
    }

    public function testSilverIsNotDominant(): void
    {
        $path = $this->testImagesDir . '/silverish.jpg';
        $this->createSolidColorImage($path, 210, 215, 225);

        $result = $this->service->extract($path);

        $this->assertNotEquals('#c0c0c0', $result['primary']);
    }

    // -------------------------------------------------------
    // Error handling
    // -------------------------------------------------------
    public function testExtractThrowsExceptionForNonExistentFile(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->service->extract('/non/existent/path.jpg');
    }
}
