<?php

declare(strict_types=1);

namespace App\ClothingItem\MessageHandler;

use App\ClothingItem\Enum\ClothingItemStatus;
use App\ClothingItem\Message\AnalyzeClothingItemMessage;
use App\Color\Analyzer\ColorExtractionApiService;
use App\Color\Matcher\ColorMatchingService;
use App\Color\Resolver\ColorFamilyResolver;
use App\Color\Service\ColorConverterService;
use App\DTO\Color\ExtractedColor;
use App\Entity\Color;
use App\Entity\ColorAnalysis;
use App\Entity\ItemColor;
use App\Repository\ClothingItemRepository;
use App\Repository\ColorRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsMessageHandler]
readonly class AnalyzeClothingItemHandler
{
    public function __construct(
        private ClothingItemRepository $clothingItemRepository,
        private ColorRepository $colorRepository,
        private ColorMatchingService $colorMatcher,
        private EntityManagerInterface $entityManager,
        private ColorExtractionApiService $apiService,
        private ColorConverterService $converter,
        private ColorFamilyResolver $colorFamilyResolver,
        #[Autowire('%clothing_analysis_dir%')]
        private string $analysisDir,
    ) {
    }

    public function __invoke(AnalyzeClothingItemMessage $message): void
    {
        $item = $this->clothingItemRepository->find($message->getClothingItemId());

        // Item isn't found → skip
        if ($item === null) {
            return;
        }

        // No photo → set status to COMPLETE, no analysis needed
        if ($item->getPhotoPath() == null) {
            $item->setStatus(ClothingItemStatus::COMPLETE);
            $this->entityManager->flush();
            return;
        }

        $imagePath = $this->analysisDir . '/' . $item->getPhotoPath();

        // Extract colors from the photo
        try {
            $extracted = $this->apiService->extractSingle($imagePath);
        } catch (
            InvalidArgumentException
            | ClientExceptionInterface
            | RedirectionExceptionInterface
            | DecodingExceptionInterface
            | ServerExceptionInterface
            | TransportExceptionInterface $e
        ) {
            // Photo isn't found → still set status to ANALYZED
            $item->setStatus(ClothingItemStatus::ANALYZED);
            $this->entityManager->flush();
            return;
        }

//        dd($extracted);

        $extractedColors = [];
        /** @var array<string, mixed> $colorArray */
        foreach ($extracted as $colorArray) {
            /** @var string $hex */
            $hex = $colorArray['hex'];

            /** @var float $weight */
            $weight = $colorArray['weight'];

            $rgb = $this->converter->hexToRgb($hex);
            $hsv = $this->converter->hexToHsv($hex);

            $extractedColors[] = new ExtractedColor(
                hex: $hex,
                r: $rgb->r,
                g: $rgb->g,
                b: $rgb->b,
                h: $hsv->h,
                s: $hsv->s,
                v: $hsv->v,
                weight: $weight,
            );
        }

        $colorAnalysis = new ColorAnalysis();
        $colorAnalysis->setClothingItem($item);
        $colorAnalysis->setExtractedColors($extractedColors);
        $this->entityManager->persist($item);

        foreach ($extractedColors as $color) {
            $family = $this->colorFamilyResolver->resolve($color);
            /** @var list<Color> $familyColors */
            $familyColors = $this->colorRepository->findByColorFamily($family);

            $matched = $this->colorMatcher->findClosest($color->hex, $familyColors);
            $item->addItemColor(new ItemColor($item, $matched));
        }

        $item->setStatus(ClothingItemStatus::ANALYZED);
        $this->entityManager->flush();
    }
}
