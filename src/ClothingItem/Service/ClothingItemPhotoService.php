<?php

declare(strict_types=1);

namespace App\ClothingItem\Service;

use App\ClothingItem\Enum\ClothingItemStatus;
use App\ClothingItem\Message\AnalyzeClothingItemMessage;
use App\ClothingItem\MessageHandler\AnalyzeClothingItemHandler;
use App\Entity\ClothingItem;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class ClothingItemPhotoService
{
    public function __construct(
        private ClothingItemPhotoUploader $photoUploader,
        private AnalyzeClothingItemHandler $handler,
        private BackgroundRemovalService $backgroundRemoval,
        #[Autowire('%clothing_analysis_dir%')]
        private string $analysisDir,
    ) {
    }

    /**
     * Replaces the analysis photo of a ClothingItem.
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
            $this->photoUploader->deleteAnalysis($item->getPhotoPath());
        }
        foreach ($item->getItemColors() as $itemColor) {
            $item->removeItemColor($itemColor);
        }

        // Upload new photo and set status to pending
        $filename = $this->photoUploader->uploadAnalysis($file);
        $item->setPhotoPath($filename);
        $item->setStatus(ClothingItemStatus::PENDING);
        $item->setPattern(null);

        /** @var int $id */
        $id = $item->getId();
        // Dispatch analysis message
        $this->handler->__invoke(new AnalyzeClothingItemMessage($id));
    }

    /**
     * Replaces the display photo of a ClothingItem.
     * - Save the display photo temporarily for rembg
     * - Removes the background from the photo
     * - Delete the old display photo and set new one
     * - Delete the temporary analysis photo - not needed anymore
     *
     * @param ClothingItem $item
     * @param UploadedFile $file
     * @return void
     */
    public function replaceDisplayPhoto(ClothingItem $item, UploadedFile $file): void
    {
        $tempFilename = $this->photoUploader->uploadAnalysis($file);
        $tempPath     = $this->analysisDir . '/' . $tempFilename;

        try {
            $displayFilename = $this->backgroundRemoval->removeBackground($tempPath);

            if ($item->getDisplayPhotoPath() !== null) {
                $this->photoUploader->deleteDisplay($item->getDisplayPhotoPath());
            }

            $item->setDisplayPhotoPath($displayFilename);
        } finally {
            $this->photoUploader->deleteAnalysis($tempFilename);
        }
    }
}
