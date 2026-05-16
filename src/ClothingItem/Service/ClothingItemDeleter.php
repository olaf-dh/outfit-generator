<?php

declare(strict_types=1);

namespace App\ClothingItem\Service;

use App\Entity\ClothingItem;
use Doctrine\ORM\EntityManagerInterface;

readonly class ClothingItemDeleter
{
    public function __construct(
        private ClothingItemPhotoUploader $photoUploader,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function delete(ClothingItem $item): void
    {
        // First delete the photo before deleting the item
        if ($item->getPhotoPath() !== null) {
            $this->photoUploader->deleteAnalysis($item->getPhotoPath());
        }

        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }
}
