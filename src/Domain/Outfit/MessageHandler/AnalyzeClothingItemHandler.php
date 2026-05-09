<?php

declare(strict_types=1);

namespace App\Domain\Outfit\MessageHandler;

use App\Domain\Outfit\Enum\ClothingItemStatus;
use App\Domain\Outfit\Message\AnalyzeClothingItemMessage;
use App\Entity\ItemColor;
use App\Repository\ClothingItemRepository;
use App\Repository\ColorRepository;
use App\Service\ColorExtractorService;
use App\Service\ColorMatchingService;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class AnalyzeClothingItemHandler
{
    public function __construct(
        private ClothingItemRepository $clothingItemRepository,
        private ColorRepository $colorRepository,
        private ColorExtractorService $colorExtractor,
        private ColorMatchingService $colorMatcher,
        private EntityManagerInterface $entityManager,
        #[Autowire('%clothing_upload_dir%')]
        private string $uploadDir,
    ) {
    }

    public function __invoke(AnalyzeClothingItemMessage $message): void
    {
        $item = $this->clothingItemRepository->find($message->getClothingItemId());

        // Item isn't found or no photo → skip
        if ($item === null || $item->getPhotoPath() === null) {
            return;
        }

        $imagePath = $this->uploadDir . '/' . $item->getPhotoPath();

        // Extract colors from the photo
        try {
            $extracted = $this->colorExtractor->extract($imagePath);
        } catch (InvalidArgumentException) {
            // Photo isn't found → still set status to ANALYZED
            $item->setStatus(ClothingItemStatus::ANALYZED);
            $this->entityManager->flush();
            return;
        }

        // Load all DB colors for the matching service
        $dbColors = $this->colorRepository->findAll();

        // Assign primary color
        $primaryHex   = $extracted['primary'];
        $primaryColor = $this->colorMatcher->findClosest($primaryHex, $dbColors);

        if ($primaryColor !== null) {
            $item->addItemColor(new ItemColor($item, $primaryColor, true));
        }

        // Assign secondary colors
        foreach ($extracted['secondary'] as $secondaryHex) {
            $secondaryColor = $this->colorMatcher->findClosest($secondaryHex, $dbColors);

            if ($secondaryColor !== null) {
                $item->addItemColor(new ItemColor($item, $secondaryColor, false));
            }
        }

        $item->setStatus(ClothingItemStatus::ANALYZED);
        $this->entityManager->flush();
    }
}
