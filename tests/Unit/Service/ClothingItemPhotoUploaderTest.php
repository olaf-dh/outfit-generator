<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\ClothingItemPhotoUploader;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ClothingItemPhotoUploaderTest extends TestCase
{
    private string $uploadDir;
    private ClothingItemPhotoUploader $uploader;

    protected function setUp(): void
    {
        // Temporary directory for tests
        $this->uploadDir = sys_get_temp_dir() . '/clothing_test_' . uniqid();
        mkdir($this->uploadDir, 0777, true);

        $this->uploader = new ClothingItemPhotoUploader($this->uploadDir);
    }

    protected function tearDown(): void
    {
        // Temporary directory cleanup after each test
        array_map('unlink', glob($this->uploadDir . '/*') ?: []);
        rmdir($this->uploadDir);
    }

    // --- Minimum valid base64 encoded image bytes ---

    private function getJpegBytes(): string
    {
        // Minimum 1x1 white JPEG
        return base64_decode(
            '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDB' .
            'kSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/wAAR' .
            'CAABAAEDASIAAhEBAxEB/8QAFAABAAAAAAAAAAAAAAAAAAAACf/EABQQAQAAAAAA' .
            'AAAAAAAAAAAAAP/EABQBAQAAAAAAAAAAAAAAAAAAAAD/xAAUEQEAAAAAAAAAAAAA' .
            'AAAAAAAA/9oADAMBAAIRAxEAPwCwABmX/9k='
        );
    }

    private function getPngBytes(): string
    {
        // Minimum 1x1 red PNG
        return base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQ' .
            'DwADhQGAWjR9awAAAABJRU5ErkJggg=='
        );
    }

    private function getWebpBytes(): string
    {
        // Minimum 1x1 WEBP
        return base64_decode(
            'UklGRlYAAABXRUJQVlA4IEoAAADQAQCdASoBAAEAAUAmJYgCdAEO/gHOAADd3gA' .
            'A5WOQ3gAAAA=='
        );
    }

    // --- Helper method: Create temporary Test-File ---

    private function makeUploadedFile(string $extension, string $bytes): UploadedFile
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_upload_');
        file_put_contents($tmpFile, $bytes);

        return new UploadedFile(
            $tmpFile,
            'test_image.' . $extension,
            null,
            null,
            true // test mode – jumps over check is_uploaded_file()
        );
    }

    private function makeLargeUploadedFile(): UploadedFile
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_upload_large_');
        // JPEG-Header + 11MB bytes → PHP recognize as JPEG
        file_put_contents($tmpFile, $this->getJpegBytes() . str_repeat('\x00', 11 * 1024 * 1024));

        return new UploadedFile($tmpFile, 'large_image.jpg', null, null, true);
    }

    // -------------------------------------------------------
    // Successful upload
    // -------------------------------------------------------

    public function testUploadReturnsFileName(): void
    {
        $file = $this->makeUploadedFile('jpg', $this->getJpegBytes());

        $fileName = $this->uploader->upload($file);

        $this->assertNotEmpty($fileName);
        $this->assertStringEndsWith('.jpg', $fileName);
    }

    public function testUploadedFileExistsOnDisk(): void
    {
        $file = $this->makeUploadedFile('jpg', $this->getJpegBytes());

        $fileName = $this->uploader->upload($file);

        $this->assertFileExists($this->uploadDir . '/' . $fileName);
    }

    public function testUploadGeneratesUniqueFilenames(): void
    {
        $file1 = $this->makeUploadedFile('jpg', $this->getJpegBytes());
        $file2 = $this->makeUploadedFile('jpg', $this->getJpegBytes());

        $fileName1 = $this->uploader->upload($file1);
        $fileName2 = $this->uploader->upload($file2);

        $this->assertNotEquals($fileName1, $fileName2);
    }

    public function testUploadAcceptsJpeg(): void
    {
        $file = $this->makeUploadedFile('jpg', $this->getJpegBytes());

        $fileName = $this->uploader->upload($file);

        $this->assertMatchesRegularExpression('/\.(jpg|jpeg)$/', $fileName);
    }

    public function testUploadAcceptsPng(): void
    {
        $file = $this->makeUploadedFile('png', $this->getPngBytes());

        $fileName = $this->uploader->upload($file);

        $this->assertStringEndsWith('.png', $fileName);
    }

    public function testUploadAcceptsWebp(): void
    {
        $file = $this->makeUploadedFile('webp', $this->getWebpBytes());

        $fileName = $this->uploader->upload($file);

        $this->assertStringEndsWith('.webp', $fileName);
    }

    // -------------------------------------------------------
    // Validation: Invalid file types
    // -------------------------------------------------------

    public function testUploadRejectsTextFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid file type');

        $file = $this->makeUploadedFile('txt', 'This is a text file without picture header.');
        $this->uploader->upload($file);
    }

    public function testUploadRejectsPdf(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid file type');

        $file = $this->makeUploadedFile('pdf', '%PDF-1.4 fake pdf content');
        $this->uploader->upload($file);
    }

    // -------------------------------------------------------
    // Validation: File size
    // -------------------------------------------------------

    public function testUploadRejectsFilesLargerThan10MB(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('File too large');

        $file = $this->makeLargeUploadedFile(); // 11MB
        $this->uploader->upload($file);
    }

    public function testUploadAcceptsFilesUpTo10MB(): void
    {
        $file = $this->makeUploadedFile('jpg', $this->getJpegBytes()); // 9MB

        $fileName = $this->uploader->upload($file);

        $this->assertNotEmpty($fileName);
    }

    // -------------------------------------------------------
    // Delete old picture
    // -------------------------------------------------------

    public function testDeleteRemovesFileFromDisk(): void
    {
        $file = $this->makeUploadedFile('jpg', $this->getJpegBytes());
        $fileName = $this->uploader->upload($file);

        $this->uploader->delete($fileName);

        $this->assertFileDoesNotExist($this->uploadDir . '/' . $fileName);
    }

    public function testDeleteNonExistentFileDoesNotThrow(): void
    {
        $this->expectNotToPerformAssertions();

        // No error when file does not exist
        $this->uploader->delete('non_existent_file.jpg');
    }
}
