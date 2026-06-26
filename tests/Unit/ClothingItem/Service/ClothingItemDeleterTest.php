<?php

declare(strict_types=1);

namespace App\Tests\Unit\ClothingItem\Service;

use App\ClothingItem\Service\ClothingItemDeleter;
use App\ClothingItem\Service\ClothingItemPhotoUploader;
use App\Entity\ClothingItem;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class ClothingItemDeleterTest extends TestCase
{
    /** @var ClothingItemPhotoUploader&MockObject */
    private ClothingItemPhotoUploader $photoUploader;

    /** @var EntityManagerInterface&MockObject */
    private EntityManagerInterface $entityManager;
    private ClothingItemDeleter $deleter;

    protected function setUp(): void
    {
        $this->photoUploader = $this->createMock(ClothingItemPhotoUploader::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->deleter = new ClothingItemDeleter(
            $this->photoUploader,
            $this->entityManager
        );
    }

    private function makeItem(?string $photoPath = null): ClothingItem
    {
        $item = new ClothingItem();
        $item->setPhotoPath($photoPath);
        return $item;
    }

    // -------------------------------------------------------
    // Item without photo
    // -------------------------------------------------------

    public function testDeleteItemWithoutPhotoDoesNotCallPhotoUploader(): void
    {
        $item = $this->makeItem(null);

        $this->photoUploader
            ->expects($this->never())
            ->method('deleteAnalysis');

        $this->entityManager->expects($this->once())->method('remove')->with($item);
        $this->entityManager->expects($this->once())->method('flush');

        $this->deleter->delete($item);
    }

    // -------------------------------------------------------
    // Item with photo
    // -------------------------------------------------------

    public function testDeleteItemWithPhotoDeletesFile(): void
    {
        $item = $this->makeItem('test.jpg');

        $this->photoUploader
            ->expects($this->once())
            ->method('deleteAnalysis')
            ->with('test.jpg');

        $this->entityManager->expects($this->once())->method('remove')->with($item);
        $this->entityManager->expects($this->once())->method('flush');

        $this->deleter->delete($item);
    }

    // -------------------------------------------------------
    // Order: first delete photo, then remove item
    // -------------------------------------------------------

    public function testPhotoIsDeletedBeforeEntityRemoval(): void
    {
        $item  = $this->makeItem('test.jpg');
        $order = [];

        $this->photoUploader
            ->expects($this->once())
            ->method('deleteAnalysis')
            ->willReturnCallback(function () use (&$order) {
                $order[] = 'photo';
            });

        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->willReturnCallback(function () use (&$order) {
                $order[] = 'remove';
            });

        $this->entityManager->expects($this->once())->method('flush');

        $this->deleter->delete($item);

        $this->assertEquals(['photo', 'remove'], $order);
    }
}
