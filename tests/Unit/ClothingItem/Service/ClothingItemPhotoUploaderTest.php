<?php

declare(strict_types=1);

namespace App\Tests\Unit\ClothingItem\Service;

use App\ClothingItem\Service\ClothingItemPhotoUploader;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ClothingItemPhotoUploaderTest extends TestCase
{
    private string $uploadDir;
    private string $analysisDir;
    private ClothingItemPhotoUploader $uploader;

    protected function setUp(): void
    {
        // Temporary directory for tests
        $this->uploadDir = sys_get_temp_dir() . '/clothing_uploads_' . uniqid();
        $this->analysisDir = sys_get_temp_dir() . '/clothing_analysis_' . uniqid();
        mkdir($this->uploadDir, 0777, true);
        mkdir($this->analysisDir, 0777, true);

        $this->uploader = new ClothingItemPhotoUploader($this->uploadDir, $this->analysisDir);
    }

    protected function tearDown(): void
    {
        // Temporary directory cleanup after each test
        array_map('unlink', glob($this->uploadDir . '/*') ?: []);
        array_map('unlink', glob($this->analysisDir . '/*') ?: []);
        rmdir($this->uploadDir);
        rmdir($this->analysisDir);
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

    // --- Helper methods: Create temporary test files ---

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
    // uploadAnalysis Tests
    // -------------------------------------------------------

    public function testUploadAnalysisReturnsFilename(): void
    {
        $file     = $this->makeUploadedFile('jpg', $this->getJpegBytes());
        $filename = $this->uploader->uploadAnalysis($file);

        $this->assertNotEmpty($filename);
    }

    public function testUploadAnalysisFileExistsInAnalysisDir(): void
    {
        $file     = $this->makeUploadedFile('jpg', $this->getJpegBytes());
        $filename = $this->uploader->uploadAnalysis($file);

        $this->assertFileExists($this->analysisDir . '/' . $filename);
    }

    public function testUploadAnalysisFileDoesNotExistInUploadsDir(): void
    {
        $file     = $this->makeUploadedFile('jpg', $this->getJpegBytes());
        $filename = $this->uploader->uploadAnalysis($file);

        $this->assertFileDoesNotExist($this->uploadDir . '/' . $filename);
    }

    // -------------------------------------------------------
    // uploadDisplay Tests
    // -------------------------------------------------------

    public function testUploadReturnsFileName(): void
    {
        $file = $this->makeUploadedFile('jpg', $this->getJpegBytes());
        $fileName = $this->uploader->uploadDisplay($file);

        $this->assertNotEmpty($fileName);
    }

    public function testUploadDisplayFileExistsInUploadsDir(): void
    {
        $file = $this->makeUploadedFile('jpg', $this->getJpegBytes());
        $fileName = $this->uploader->uploadDisplay($file);

        $this->assertFileExists($this->uploadDir . '/' . $fileName);
    }

    public function testUploadDisplayFileDoesNotExistInAnalysisDir(): void
    {
        $file     = $this->makeUploadedFile('jpg', $this->getJpegBytes());
        $filename = $this->uploader->uploadDisplay($file);

        $this->assertFileDoesNotExist($this->analysisDir . '/' . $filename);
    }

    // -------------------------------------------------------
    // Validation
    // -------------------------------------------------------
    public function testUploadAnalysisAcceptsJpeg(): void
    {
        $file     = $this->makeUploadedFile('jpg', $this->getJpegBytes());
        $filename = $this->uploader->uploadAnalysis($file);

        $this->assertMatchesRegularExpression('/\.(jpg|jpeg)$/', $filename);
    }

    public function testUploadAnalysisAcceptsPng(): void
    {
        $file     = $this->makeUploadedFile('png', $this->getPngBytes());
        $filename = $this->uploader->uploadAnalysis($file);

        $this->assertStringEndsWith('.png', $filename);
    }

    public function testUploadAnalysisAcceptsWebp(): void
    {
        $file     = $this->makeUploadedFile('webp', $this->getWebpBytes());
        $filename = $this->uploader->uploadAnalysis($file);

        $this->assertStringEndsWith('.webp', $filename);
    }

    public function testUploadRejectsTextFile(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid file type');

        $tmpFile = tempnam(sys_get_temp_dir(), 'test_txt_');
        file_put_contents($tmpFile, 'Das ist ein Textdokument.');
        $file = new UploadedFile($tmpFile, 'test.txt', null, null, true);

        $this->uploader->uploadAnalysis($file);
    }

    public function testUploadRejectsFilesLargerThan10MB(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('File too large');

        $this->uploader->uploadAnalysis($this->makeLargeUploadedFile());
    }

    public function testUploadAcceptsFilesUpTo10MB(): void
    {
        $file = $this->makeUploadedFile('jpg', $this->getJpegBytes()); // 9MB

        $fileName = $this->uploader->uploadAnalysis($file);

        $this->assertNotEmpty($fileName);
    }

    public function testUploadGeneratesUniqueFilenames(): void
    {
        $filename1 = $this->uploader->uploadAnalysis($this->makeUploadedFile('jpg', $this->getJpegBytes()));
        $filename2 = $this->uploader->uploadAnalysis($this->makeUploadedFile('jpg', $this->getJpegBytes()));

        $this->assertNotEquals($filename1, $filename2);
    }

    // -------------------------------------------------------
    // deleteAnalysis Tests
    // -------------------------------------------------------
    public function testDeleteAnalysisRemovesFileFromAnalysisDir(): void
    {
        $file     = $this->makeUploadedFile('jpg', $this->getJpegBytes());
        $filename = $this->uploader->uploadAnalysis($file);

        $this->uploader->deleteAnalysis($filename);

        $this->assertFileDoesNotExist($this->analysisDir . '/' . $filename);
    }

    public function testDeleteAnalysisNonExistentFileDoesNotThrow(): void
    {
        $this->expectNotToPerformAssertions();
        $this->uploader->deleteAnalysis('non_existent_file.jpg');
    }

    // -------------------------------------------------------
    // deleteDisplay Tests
    // -------------------------------------------------------
    public function testDeleteDisplayRemovesFileFromUploadsDir(): void
    {
        $file     = $this->makeUploadedFile('jpg', $this->getJpegBytes());
        $filename = $this->uploader->uploadDisplay($file);

        $this->uploader->deleteDisplay($filename);

        $this->assertFileDoesNotExist($this->uploadDir . '/' . $filename);
    }

    public function testDeleteDisplayNonExistentFileDoesNotThrow(): void
    {
        $this->expectNotToPerformAssertions();
        $this->uploader->deleteDisplay('non_existent_file.jpg');
    }
}
