<?php

declare(strict_types=1);

namespace App\Tests\Unit\ClothingItem\Service;

use App\ClothingItem\Enum\ClothingItemStatus;
use App\ClothingItem\Message\AnalyzeClothingItemMessage;
use App\ClothingItem\MessageHandler\AnalyzeClothingItemHandler;
use App\ClothingItem\Service\BackgroundRemovalService;
use App\ClothingItem\Service\ClothingItemPhotoService;
use App\ClothingItem\Service\ClothingItemPhotoUploader;
use App\Entity\ClothingItem;
use App\Entity\Color;
use App\Entity\ItemColor;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[AllowMockObjectsWithoutExpectations]
class ClothingItemPhotoServiceTest extends TestCase
{
    /** @var ClothingItemPhotoUploader&MockObject */
    private ClothingItemPhotoUploader $photoUploader;

    /** @var AnalyzeClothingItemHandler&MockObject */
    private AnalyzeClothingItemHandler $handler;

    /** @var BackgroundRemovalService&MockObject */
    private BackgroundRemovalService $backgroundRemoval;

    private ClothingItemPhotoService $service;

    protected function setUp(): void
    {
        $this->photoUploader = $this->createMock(ClothingItemPhotoUploader::class);
        $this->handler       = $this->createMock(AnalyzeClothingItemHandler::class);
        $this->backgroundRemoval = $this->createMock(BackgroundRemovalService::class);

        $this->service = new ClothingItemPhotoService(
            $this->photoUploader,
            $this->handler,
            $this->backgroundRemoval,
            '/tmp/analysis/'
        );
    }

    // --- Helper methods ---

    private function makeItem(int $id, ?string $photoPath = null, ?string $displayPhotoPath = null): ClothingItem
    {
        $item = new ClothingItem();
        $item->setPhotoPath($photoPath);
        $item->setDisplayPhotoPath($displayPhotoPath);
        $item->setStatus(ClothingItemStatus::COMPLETE);

        $ref  = new ReflectionClass($item);
        $prop = $ref->getProperty('id');
        $prop->setValue($item, 1);

        return $item;
    }

    private function makeUploadedFile(): UploadedFile
    {
        return $this->createStub(UploadedFile::class);
    }

    // -------------------------------------------------------
    // New photo without old photo
    // -------------------------------------------------------

    public function testReplacePhotoUploadsNewFile(): void
    {
        $item = $this->makeItem(1);
        $file = $this->makeUploadedFile();

        $this->photoUploader
            ->expects($this->once())
            ->method('uploadAnalysis')
            ->with($file)
            ->willReturn('new-photo.jpg');

        $this->service->replacePhoto($item, $file);

        $this->assertEquals('new-photo.jpg', $item->getPhotoPath());
    }

    public function testReplacePhotoSetsStatusToPending(): void
    {
        $item = $this->makeItem(1);
        $file = $this->makeUploadedFile();

        $this->photoUploader->method('uploadAnalysis')->willReturn('new-photo.jpg');

        $this->service->replacePhoto($item, $file);

        $this->assertEquals(ClothingItemStatus::PENDING, $item->getStatus());
    }

    public function testReplacePhotoDispatchesAnalysisMessage(): void
    {
        $item = $this->makeItem(1);
        $file = $this->makeUploadedFile();

        $this->photoUploader->method('uploadAnalysis')->willReturn('new-photo.jpg');

        $this->handler
            ->expects($this->once())
            ->method('__invoke')
            ->with($this->isInstanceOf(AnalyzeClothingItemMessage::class));

        $this->service->replacePhoto($item, $file);
    }

    // -------------------------------------------------------
    // Existing photo is replaced
    // -------------------------------------------------------

    public function testReplacePhotoDeletesOldFile(): void
    {
        $item = $this->makeItem(1, 'old-photo.jpg');
        $file = $this->makeUploadedFile();

        $this->photoUploader
            ->expects($this->once())
            ->method('deleteAnalysis')
            ->with('old-photo.jpg');

        $this->photoUploader->method('uploadAnalysis')->willReturn('new-photo.jpg');

        $this->service->replacePhoto($item, $file);
    }

    public function testReplacePhotoRemovesOldColors(): void
    {
        $item  = $this->makeItem(1, 'old-photo.jpg');
        $color = new Color();
        $ic    = new ItemColor($item, $color, true);
        $item->addItemColor($ic);

        $file = $this->makeUploadedFile();
        $this->photoUploader->method('uploadAnalysis')->willReturn('new-photo.jpg');

        $this->service->replacePhoto($item, $file);

        $this->assertCount(0, $item->getItemColors());
    }

    public function testReplacePhotoWithNoOldPhotoDoesNotCallDelete(): void
    {
        $item = $this->makeItem(1);
        $file = $this->makeUploadedFile();

        $this->photoUploader
            ->expects($this->never())
            ->method('deleteAnalysis');

        $this->photoUploader->method('uploadAnalysis')->willReturn('new-photo.jpg');

        $this->service->replacePhoto($item, $file);
    }

    // --------------------------------------------------------------------
    // Replace display photo
    // --------------------------------------------------------------------

    public function testReplaceDisplayPhotoSetsDisplayPhotoPath(): void
    {
        $item = $this->makeItem(1, 'analysis.jpg');
        $file = $this->makeUploadedFile();

        $this->photoUploader->method('uploadAnalysis')->willReturn('temp.jpg');
        $this->backgroundRemoval->method('removeBackground')->willReturn('display.png');

        $this->service->replaceDisplayPhoto($item, $file);

        $this->assertEquals('display.png', $item->getDisplayPhotoPath());
    }

    public function testReplaceDisplayPhotoDeletesOldDisplayPhoto(): void
    {
        $item = $this->makeItem(1, 'analysis.jpg', 'old-display.png');
        $file = $this->makeUploadedFile();

        $this->photoUploader->method('uploadAnalysis')->willReturn('temp.jpg');
        $this->backgroundRemoval->method('removeBackground')->willReturn('new-display.png');

        $this->photoUploader
            ->expects($this->once())
            ->method('deleteDisplay')
            ->with('old-display.png');

        $this->service->replaceDisplayPhoto($item, $file);
    }

    public function testReplaceDisplayPhotoDeletesTempFileAfterSuccess(): void
    {
        $item = $this->makeItem(1, 'analysis.jpg');
        $file = $this->makeUploadedFile();

        $this->photoUploader->method('uploadAnalysis')->willReturn('temp.jpg');
        $this->backgroundRemoval->method('removeBackground')->willReturn('display.png');

        $this->photoUploader
            ->expects($this->once())
            ->method('deleteAnalysis')
            ->with('temp.jpg');

        $this->service->replaceDisplayPhoto($item, $file);
    }

    public function testReplaceDisplayPhotoDeletesTempFileEvenOnFailure(): void
    {
        $item = $this->makeItem(1, 'analysis.jpg');
        $file = $this->makeUploadedFile();

        $this->photoUploader->method('uploadAnalysis')->willReturn('temp.jpg');
        $this->backgroundRemoval
            ->method('removeBackground')
            ->willThrowException(new RuntimeException('rembg failed'));

        $this->photoUploader
            ->expects($this->once())
            ->method('deleteAnalysis')
            ->with('temp.jpg');

        $this->expectException(RuntimeException::class);

        $this->service->replaceDisplayPhoto($item, $file);
    }

    public function testReplaceDisplayPhotoWithNoOldDisplayPhotoDoesNotCallDeleteDisplay(): void
    {
        $item = $this->makeItem(1, 'analysis.jpg');
        $file = $this->makeUploadedFile();

        $this->photoUploader->method('uploadAnalysis')->willReturn('temp.jpg');
        $this->backgroundRemoval->method('removeBackground')->willReturn('display.png');

        $this->photoUploader->expects($this->never())->method('deleteDisplay');

        $this->service->replaceDisplayPhoto($item, $file);
    }
}
