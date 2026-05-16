<?php

declare(strict_types=1);

namespace App\Tests\Unit\ClothingItem\Service;

use App\ClothingItem\Enum\ClothingItemStatus;
use App\ClothingItem\Service\BatchReviewProgressService;
use App\Entity\ClothingItem;
use App\Entity\User;
use App\Repository\ClothingItemRepository;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

#[AllowMockObjectsWithoutExpectations]
class BatchReviewProgressServiceTest extends TestCase
{
    /** @var ClothingItemRepository&MockObject */
    private ClothingItemRepository $repository;

    private BatchReviewProgressService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ClothingItemRepository::class);
        $this->service    = new BatchReviewProgressService($this->repository);
    }

    // --- Helper method ---

    private function makeItem(int $id): ClothingItem
    {
        $item = new ClothingItem();
        $item->setStatus(ClothingItemStatus::ANALYZED);

        $ref  = new ReflectionClass($item);
        $prop = $ref->getProperty('id');
        $prop->setValue($item, $id);

        return $item;
    }

    // -------------------------------------------------------
    // Calculate progress
    // -------------------------------------------------------

    public function testGetProgressReturnsCorrectTotal(): void
    {
        $user  = new User();
        $items = [$this->makeItem(1), $this->makeItem(2), $this->makeItem(3)];

        $this->repository->method('findUnreviewedByOwner')->willReturn($items);

        [$total, $position] = $this->service->getProgress($user, $items[0]);

        $this->assertEquals(3, $total);
    }

    public function testGetProgressReturnsCorrectPosition(): void
    {
        $user  = new User();
        $items = [$this->makeItem(1), $this->makeItem(2), $this->makeItem(3)];

        $this->repository->method('findUnreviewedByOwner')->willReturn($items);

        [$total, $position] = $this->service->getProgress($user, $items[1]);

        $this->assertEquals(2, $position);
    }

    public function testGetProgressReturnsFirstPosition(): void
    {
        $user  = new User();
        $items = [$this->makeItem(1), $this->makeItem(2)];

        $this->repository->method('findUnreviewedByOwner')->willReturn($items);

        [$total, $position] = $this->service->getProgress($user, $items[0]);

        $this->assertEquals(1, $position);
    }

    public function testGetProgressReturnsLastPosition(): void
    {
        $user  = new User();
        $items = [$this->makeItem(1), $this->makeItem(2), $this->makeItem(3)];

        $this->repository->method('findUnreviewedByOwner')->willReturn($items);

        [$total, $position] = $this->service->getProgress($user, $items[2]);

        $this->assertEquals(3, $position);
    }

    public function testGetProgressWithEmptyListReturnsZeros(): void
    {
        $user = new User();
        $item = $this->makeItem(1);

        $this->repository->method('findUnreviewedByOwner')->willReturn([]);

        [$total, $position] = $this->service->getProgress($user, $item);

        $this->assertEquals(0, $total);
        $this->assertEquals(0, $position);
    }

    public function testGetProgressWithItemNotInListReturnsZeroPosition(): void
    {
        $user  = new User();
        $items = [$this->makeItem(1), $this->makeItem(2)];
        $other = $this->makeItem(99);

        $this->repository->method('findUnreviewedByOwner')->willReturn($items);

        [$total, $position] = $this->service->getProgress($user, $other);

        $this->assertEquals(2, $total);
        $this->assertEquals(0, $position);
    }
}
