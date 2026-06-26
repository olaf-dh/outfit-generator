<?php

declare(strict_types=1);

namespace App\Tests\Unit\ClothingItem\MessageHandler;

use App\ClothingItem\Enum\ClothingItemStatus;
use App\ClothingItem\Message\AnalyzeClothingItemMessage;
use App\ClothingItem\MessageHandler\AnalyzeClothingItemHandler;
use App\Color\Analyzer\ColorExtractionApiService;
use App\Color\Matcher\ColorMatchingService;
use App\Color\Resolver\ColorFamilyResolver;
use App\Color\Service\ColorConverterService;
use App\DTO\Color\HsvColor;
use App\DTO\Color\RgbColor;
use App\Entity\ClothingItem;
use App\Repository\ClothingItemRepository;
use App\Repository\ColorRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @group unit
 */
final class AnalyzeClothingItemHandlerTest extends TestCase
{
    /** @var ClothingItemRepository&MockObject */
    private ClothingItemRepository $clothingItemRepository;

    /** @var ColorRepository&MockObject */
    private ColorRepository $colorRepository;

    /** @var ColorExtractionApiService&MockObject */
    private ColorExtractionApiService $apiService;

    /** @var ColorMatchingService&MockObject */
    private ColorMatchingService $colorMatcher;

    /** @var AnalyzeClothingItemHandler */
    private AnalyzeClothingItemHandler $handler;

    protected function setUp(): void
    {
        $this->clothingItemRepository = $this->createMock(ClothingItemRepository::class);
        $this->colorRepository        = $this->createMock(ColorRepository::class);
        $this->apiService             = $this->createMock(ColorExtractionApiService::class);
        $this->colorMatcher           = $this->createMock(ColorMatchingService::class);
        $colorConverter               = $this->createStub(ColorConverterService::class);
        $entityManager                = $this->createStub(EntityManagerInterface::class);
        $colorFamilyResolver          = new ColorFamilyResolver();

        $colorConverter
            ->method('hexToRgb')
            ->willReturn(new RgbColor(255, 0, 0));
        $colorConverter
            ->method('hexToHsv')
            ->willReturn(new HsvColor(0, 100, 100));

        $this->handler = new AnalyzeClothingItemHandler(
            $this->clothingItemRepository,
            $this->colorRepository,
            $this->colorMatcher,
            $entityManager,
            $this->apiService,
            $colorConverter,
            $colorFamilyResolver,
            sys_get_temp_dir()
        );
    }

    // --- Helper method ---

    private function makeClothingItem(int $id, ?string $photoPath = 'test.jpg'): ClothingItem
    {
        $item = new ClothingItem();
        $item->setPhotoPath($photoPath);
        $item->setStatus(ClothingItemStatus::PENDING);

        // Set ID per reflection, otherwise the ID is null in the DB
        $ref = new ReflectionClass($item);
        $prop = $ref->getProperty('id');
        $prop->setValue($item, $id);

        return $item;
    }

    // -------------------------------------------------------
    // Item not found
    // -------------------------------------------------------

    public function testHandlerSkipsIfItemNotFound(): void
    {
        $this->clothingItemRepository
            ->expects($this->once())
            ->method('find')
            ->willReturn(null);

        $this->apiService
            ->expects($this->never())
            ->method('extractSingle');

        $this->colorRepository
            ->expects($this->never())
            ->method('findAll');

        $this->colorMatcher
            ->expects($this->never())
            ->method('findClosest');

        $this->handler->__invoke(new AnalyzeClothingItemMessage(999));
    }

    // -------------------------------------------------------
    // Item without photo
    // -------------------------------------------------------

    public function testHandlerSkipsIfNoPhotoPath(): void
    {
        $item = $this->makeClothingItem(1, null);

        $this->clothingItemRepository
            ->expects($this->once())
            ->method('find')
            ->willReturn($item);

        $this->apiService
            ->expects($this->never())
            ->method('extractSingle');

        $this->colorRepository
            ->expects($this->never())
            ->method('findAll');

        $this->colorMatcher
            ->expects($this->never())
            ->method('findClosest');

        $this->handler->__invoke(new AnalyzeClothingItemMessage(1));
    }
}
