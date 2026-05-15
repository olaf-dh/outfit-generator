<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Domain\Outfit\Enum\ClothingItemStatus;
use App\Domain\Outfit\Message\AnalyzeClothingItemMessage;
use App\Domain\Outfit\MessageHandler\AnalyzeClothingItemHandler;
use App\Entity\ClothingItem;
use App\Entity\Color;
use App\Entity\ItemColor;
use App\Service\ClothingItemPhotoService;
use App\Service\ClothingItemPhotoUploader;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[AllowMockObjectsWithoutExpectations]
class ClothingItemPhotoServiceTest extends TestCase
{
    /** @var ClothingItemPhotoUploader&MockObject */
    private ClothingItemPhotoUploader $photoUploader;

    /** @var AnalyzeClothingItemHandler&MockObject */
    private AnalyzeClothingItemHandler $handler;

    private ClothingItemPhotoService $service;

    protected function setUp(): void
    {
        $this->photoUploader = $this->createMock(ClothingItemPhotoUploader::class);
        $this->handler       = $this->createMock(AnalyzeClothingItemHandler::class);

        $this->service = new ClothingItemPhotoService(
            $this->photoUploader,
            $this->handler
        );
    }

    // --- Helper methods ---

    private function makeItem(?string $photoPath = null): ClothingItem
    {
        $item = new ClothingItem();
        $item->setPhotoPath($photoPath);
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
        $item = $this->makeItem(null);
        $file = $this->makeUploadedFile();

        $this->photoUploader
            ->expects($this->once())
            ->method('upload')
            ->with($file)
            ->willReturn('new-photo.jpg');

        $this->service->replacePhoto($item, $file);

        $this->assertEquals('new-photo.jpg', $item->getPhotoPath());
    }

    public function testReplacePhotoSetsStatusToPending(): void
    {
        $item = $this->makeItem(null);
        $file = $this->makeUploadedFile();

        $this->photoUploader->method('upload')->willReturn('new-photo.jpg');

        $this->service->replacePhoto($item, $file);

        $this->assertEquals(ClothingItemStatus::PENDING, $item->getStatus());
    }

    public function testReplacePhotoDispatchesAnalysisMessage(): void
    {
        $item = $this->makeItem(null);
        $file = $this->makeUploadedFile();

        $this->photoUploader->method('upload')->willReturn('new-photo.jpg');

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
        $item = $this->makeItem('old-photo.jpg');
        $file = $this->makeUploadedFile();

        $this->photoUploader
            ->expects($this->once())
            ->method('delete')
            ->with('old-photo.jpg');

        $this->photoUploader->method('upload')->willReturn('new-photo.jpg');

        $this->service->replacePhoto($item, $file);
    }

    public function testReplacePhotoRemovesOldColors(): void
    {
        $item  = $this->makeItem('old-photo.jpg');
        $color = new Color();
        $ic    = new ItemColor($item, $color, true);
        $item->addItemColor($ic);

        $file = $this->makeUploadedFile();
        $this->photoUploader->method('upload')->willReturn('new-photo.jpg');

        $this->service->replacePhoto($item, $file);

        $this->assertCount(0, $item->getItemColors());
    }

    public function testReplacePhotoWithNoOldPhotoDoesNotCallDelete(): void
    {
        $item = $this->makeItem(null);
        $file = $this->makeUploadedFile();

        $this->photoUploader
            ->expects($this->never())
            ->method('delete');

        $this->photoUploader->method('upload')->willReturn('new-photo.jpg');

        $this->service->replacePhoto($item, $file);
    }
}
