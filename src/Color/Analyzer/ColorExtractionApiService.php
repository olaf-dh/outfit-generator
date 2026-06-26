<?php

declare(strict_types=1);

namespace App\Color\Analyzer;

use Exception;
use RuntimeException;
use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ColorExtractionApiService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private string $colorApiUrl,
    ) {
        $this->colorApiUrl = rtrim($colorApiUrl, '/');
    }

    /**
     * Extracts colors from a single image.
     *
     * @return array<int, array<string, mixed>>
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface|DecodingExceptionInterface
     */
    public function extractSingle(string $imagePath, int $nColors = 5): array
    {
        if (!file_exists($imagePath)) {
            throw new InvalidArgumentException('Image file not found: ' . $imagePath);
        }

        $handle = fopen($imagePath, 'r');

        if ($handle === false) {
            throw new InvalidArgumentException('Could not open image file: ' . $imagePath);
        }

        try {
            $response = $this->httpClient->request('POST', $this->colorApiUrl . '/extract', [
                'headers' => ['Content-Type' => 'multipart/form-data'],
                'body' => [
                    'file' => $handle,
                    'n_colors' => $nColors,
                ]
            ]);

            $data = $response->toArray();

            if (empty($data['colors'])) {
                throw new InvalidArgumentException('No colors extracted from image: ' . $imagePath);
            }

            /** @var array<int, array<string, mixed>> $colors */
            $colors = $data['colors'];

            return $colors;
        } catch (Exception $e) {
            throw new InvalidArgumentException(
                sprintf(
                    'Error extracting colors from image: %s',
                    $e->getMessage()
                ),
                previous: $e
            );
        } finally {
            fclose($handle);
        }
    }

    /**
     * Processes a batch of images and extracts colors.
     *
     * @param list<UploadedFile> $files
     * @param int $nColors
     * @return array{
     *     job_id: string,
     *     status: string,
     *     message: string
     * }|array{
     *     results: list<array{
     *         image: string,
     *         colors: list<array{
     *             hex: string,
     *             rgb: list{int},
     *             hsv: list{float},
     *         }>,
     *         status: string
     *     }>
     * }
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function extractBatch(array $files, int $nColors = 5): array
    {
        try {
            // 1. Batch-Job starten
            $body = [];
            foreach ($files as $file) {
                $body[] = ['name' => 'files[]', 'contents' => fopen($file->getRealPath(), 'r')];
            }

            $response = $this->httpClient->request('POST', $this->colorApiUrl . '/batch', [
                'headers' => ['Content-Type' => 'multipart/form-data'],
                'body' => $body
            ]);

            /** @var array{job_id: string, status: string, message: string} $jobData */
            $jobData = $response->toArray();
            $jobId = $jobData['job_id'];

            // 2. Ergebnisse abrufen (Polling)
            $maxAttempts = 30;

            for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
                sleep(1); // 1 Sekunde warten
                $resultResponse = $this->httpClient->request('GET', $this->colorApiUrl . "/batch/$jobId");

                /** @var array{results: list<array{image: string, colors: list<array{hex: string, rgb: list{int}, hsv: list{float}}>, status: string}>} $result */
                $result = $resultResponse->toArray();

                if ($result['results'] != null) {
                    return $result;
                }
            }
        } catch (
            ClientExceptionInterface
            | DecodingExceptionInterface | RedirectionExceptionInterface
            | ServerExceptionInterface | TransportExceptionInterface $e
        ) {
        }
        throw new RuntimeException('Batch-Job konnte nicht abgeschlossen werden.');
    }
}
