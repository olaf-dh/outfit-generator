<?php

declare(strict_types=1);

namespace App\ClothingItem\Service;

use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

readonly class ClothingItemPhotoUploader
{
    private const array ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    private const int|float MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB in Bytes

    public function __construct(
        #[Autowire('%clothing_upload_dir%')]
        private string $uploadDir,
        #[Autowire('%clothing_analysis_dir%')]
        private string $analysisDir,
    ) {
    }

    /**
     * Validates and saves an uploaded photos
     * Returns generated file name
     *
     * @throws InvalidArgumentException when file type or file size is invalid
     */
    public function uploadAnalysis(UploadedFile $file): string
    {
        $this->validate($file);
        $filename = $this->generateFilename($file);
        $file->move($this->analysisDir, $filename);
        return $filename;
    }

    public function uploadDisplay(UploadedFile $file): string
    {
        $this->validate($file);
        $filename = $this->generateFilename($file);
        $file->move($this->uploadDir, $filename);
        return $filename;
    }

    /**
     * Deletes a photo from file system
     * Does not throw an exception when file does not exist
     */
    public function deleteAnalysis(string $filename): void
    {
        $path = $this->analysisDir . '/' . $filename;
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function deleteDisplay(string $filename): void
    {
        $path = $this->uploadDir . '/' . $filename;
        if (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * Returns the complete path to a photo
     */
    public function getPath(string $fileName): string
    {
        return $this->uploadDir . '/' . $fileName;
    }

    private function validate(UploadedFile $uploadedFile): void
    {
        if (!in_array($uploadedFile->getMimeType(), self::ALLOWED_MIME_TYPES, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid file type "%s". Allowed types: %s',
                    $uploadedFile->getMimeType(),
                    implode(', ', self::ALLOWED_MIME_TYPES)
                )
            );
        }

        if ($uploadedFile->getSize() > self::MAX_FILE_SIZE) {
            throw new InvalidArgumentException(
                sprintf(
                    'File too large (%d bytes). Maximum allowed size is %d bytes.',
                    $uploadedFile->getSize(),
                    self::MAX_FILE_SIZE
                )
            );
        }
    }

    private function generateFilename(UploadedFile $uploadedFile): string
    {
        $extension = $uploadedFile->guessExtension() ?? 'jpg';

        return sprintf('%s.%s', Uuid::v4()->toRfc4122(), $extension);
    }
}
