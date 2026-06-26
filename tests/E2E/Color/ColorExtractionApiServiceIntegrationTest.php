<?php

declare(strict_types=1);

namespace App\Tests\E2E\Color;

use App\Color\Analyzer\ColorExtractionApiService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @group e2e
 */
final class ColorExtractionApiServiceIntegrationTest extends KernelTestCase
{
    private string $imagePath;

    protected function setUp(): void
    {
        $dir = sys_get_temp_dir() . '/integration_color_test';

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $this->imagePath = $dir . '/red.png';
        $image = imagecreatetruecolor(100, 100);

        self::assertNotFalse($image);
        $red = imagecolorallocate($image, 255, 0, 0);

        self::assertNotFalse($red);
        imagefill($image, 0, 0, $red);
        imagepng($image, $this->imagePath);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testExtractSingleReturnsColors(): void
    {
        /** @var ColorExtractionApiService $service */
        $service = self::getContainer()->get(ColorExtractionApiService::class);

        /** @var array<int, array<string, mixed>> $colors */
        $colors = $service->extractSingle(
            $this->imagePath,
            1
        );

        self::assertNotEmpty($colors);

        /** @var string $hex */
        $hex = $colors[0]['hex'];

        self::assertMatchesRegularExpression(
            '/^#[0-9a-f]{6}$/',
            $hex
        );
    }
}
