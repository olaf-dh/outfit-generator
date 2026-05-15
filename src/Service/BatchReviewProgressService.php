<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ClothingItem;
use App\Repository\ClothingItemRepository;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class BatchReviewProgressService
{
    public function __construct(
        private ClothingItemRepository $repository,
    ) {
    }

    /**
     * @return array{0: int, 1: int} [total, position]
     */
    public function getProgress(UserInterface $owner, ClothingItem $currentItem): array
    {
        $items = $this->repository->findUnreviewedByOwner($owner);
        $total = count($items);

        if ($total === 0) {
            return [0, 0];
        }

        $position = 0;
        foreach ($items as $index => $item) {
            if ($item->getId() === $currentItem->getId()) {
                $position = $index + 1;
                break;
            }
        }

        return [$total, $position];
    }
}
