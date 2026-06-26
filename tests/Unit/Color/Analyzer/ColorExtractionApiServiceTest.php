<?php

declare(strict_types=1);

namespace App\Tests\Unit\Color\Analyzer;

use App\Color\Analyzer\ColorExtractionApiService;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @group unit
 */
class ColorExtractionApiServiceTest extends TestCase
{
    private string $testImagesDir;

    protected function setUp(): void
    {
        $this->testImagesDir = sys_get_temp_dir() . '/color_extraction_api_test_' . uniqid();
        mkdir($this->testImagesDir, 0777, true);
    }

    protected function tearDown(): void
    {
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
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface|DecodingExceptionInterface
     */
    public function testExtractSingleReturnsColors(): void
    {
        $imagePath = $this->testImagesDir . '/shirt.jpg';
        $this->createSolidColorImage($imagePath, 56, 56, 56); // Dark gray

        $response = $this->createStub(ResponseInterface::class);

        $response
            ->method('toArray')
            ->willReturn([
                'colors' => [
                    ['hex' => '#383838'],
                ],
            ]);

        $client = $this->createMock(HttpClientInterface::class);

        $client
            ->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'http://localhost:5000/extract',
                $this->callback(
                    function (mixed $options): bool {
                        if (!is_array($options)) {
                            return false;
                        }
                        $body = $options['body'] ?? null;

                        if (!is_array($body)) {
                            return false;
                        }

                        return isset($body['file'])
                            && ($body['n_colors'] ?? null) === 1;
                    }
                )
            )
            ->willReturn($response);

        $service = new ColorExtractionApiService($client, 'http://localhost:5000');

        $result = $service->extractSingle($imagePath, 1);

        self::assertSame(
            [['hex' => '#383838']],
            $result
        );
    }
}
