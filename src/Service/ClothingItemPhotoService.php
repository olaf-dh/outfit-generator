<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Outfit\Enum\ClothingItemStatus;
use App\Domain\Outfit\Message\AnalyzeClothingItemMessage;
use App\Domain\Outfit\MessageHandler\AnalyzeClothingItemHandler;
use App\Entity\ClothingItem;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class ClothingItemPhotoService
{
    public function __construct(
        private ClothingItemPhotoUploader $photoUploader,
        private AnalyzeClothingItemHandler $handler,
    ) {
    }

    /**
     * Replaces the photo of a ClothingItem.
     * - Deletes the old photo from the file system
     * - Deletes all existing item colors
     * - Uploads the new photo
     * - Sets the status to PENDING
     * - Dispatches the analysis
     *
     * @throws InvalidArgumentException if data type is not supported
     */
    public function replacePhoto(ClothingItem $item, UploadedFile $file): void
    {
        // Delete old photo and color associations
        if ($item->getPhotoPath() !== null) {
            $this->photoUploader->delete($item->getPhotoPath());

            foreach ($item->getItemColors() as $itemColor) {
                $item->removeItemColor($itemColor);
            }
        }

        // Upload new photo and set status to pending
        $filename = $this->photoUploader->upload($file);
        $item->setPhotoPath($filename);
        $item->setStatus(ClothingItemStatus::PENDING);

        /** @var int $id */
        $id = $item->getId();
        // Dispatch analysis message
        $this->handler->__invoke(new AnalyzeClothingItemMessage($id));
    }
}
