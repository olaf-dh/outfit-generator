<?php

namespace App\Tests\Unit\Domain\Outfit\MessageHandler;

use App\Entity\ClothingItem;
use App\Entity\Color;
use App\Entity\ItemColor;
use App\Domain\Outfit\Enum\ClothingItemStatus;
use App\Domain\Outfit\Enum\ColorFamily;
use App\Domain\Outfit\Enum\ColorSaturation;
use App\Domain\Outfit\Enum\ColorTemperature;
use App\Domain\Outfit\Enum\ColorTone;
use App\Domain\Outfit\Message\AnalyzeClothingItemMessage;
use App\Domain\Outfit\MessageHandler\AnalyzeClothingItemHandler;
use App\Repository\ClothingItemRepository;
use App\Repository\ColorRepository;
use App\Service\ColorExtractorService;
use App\Service\ColorMatchingService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AnalyzeClothingItemHandlerTest extends TestCase
{
    /** @var ClothingItemRepository&MockObject */
    private ClothingItemRepository $clothingItemRepository;

    /** @var ColorRepository&MockObject */
    private ColorRepository $colorRepository;

    /** @var ColorExtractorService&MockObject */
    private ColorExtractorService $colorExtractor;

    /** @var ColorMatchingService&MockObject */
    private ColorMatchingService $colorMatcher;

    /** @var AnalyzeClothingItemHandler */
    private AnalyzeClothingItemHandler $handler;

    protected function setUp(): void
    {
        $this->clothingItemRepository = $this->createMock(ClothingItemRepository::class);
        $this->colorRepository        = $this->createMock(ColorRepository::class);
        $this->colorExtractor         = $this->createMock(ColorExtractorService::class);
        $this->colorMatcher           = $this->createMock(ColorMatchingService::class);
        $entityManager = $this->createStub(EntityManagerInterface::class);

        $this->handler = new AnalyzeClothingItemHandler(
            $this->clothingItemRepository,
            $this->colorRepository,
            $this->colorExtractor,
            $this->colorMatcher,
            $entityManager,
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

    private function makeColor(string $name, string $hex): Color
    {
        $color = new Color();
        $color->setName($name);
        $color->setHexCode($hex);
        $color->setFamily(ColorFamily::GRAY);
        $color->setTone(ColorTone::DARK);
        $color->setTemperature(ColorTemperature::COOL);
        $color->setSaturation(ColorSaturation::MUTED);
        return $color;
    }

    // -------------------------------------------------------
    // Item not found
    // -------------------------------------------------------

    public function testHandlerSkipsIfItemNotFound(): void
    {
//        $this->expectNotToPerformAssertions();

        $this->clothingItemRepository
            ->expects($this->once())
            ->method('find')
            ->willReturn(null);

        $this->colorExtractor
            ->expects($this->never())
            ->method('extract');

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
//        $this->expectNotToPerformAssertions();

        $item = $this->makeClothingItem(1, null);

        $this->clothingItemRepository
            ->expects($this->once())
            ->method('find')
            ->willReturn($item);

        $this->colorExtractor
            ->expects($this->never())
            ->method('extract');

        $this->colorRepository
            ->expects($this->never())
            ->method('findAll');

        $this->colorMatcher
            ->expects($this->never())
            ->method('findClosest');

        $this->handler->__invoke(new AnalyzeClothingItemMessage(1));
    }

    // -------------------------------------------------------
    // Set status to ANALYZED
    // -------------------------------------------------------

    public function testHandlerSetsStatusToAnalyzed(): void
    {
        $item  = $this->makeClothingItem(1);
        $color = $this->makeColor('anthracite', '#383838');

        $this->clothingItemRepository
            ->expects($this->once())
            ->method('find')
            ->willReturn($item);

        $this->colorExtractor
            ->expects($this->once())
            ->method('extract')
            ->willReturn(['primary' => '#383838', 'secondary' => []]);

        $this->colorRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$color]);

        $this->colorMatcher
            ->expects($this->once())
            ->method('findClosest')
            ->willReturn($color);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->once())
            ->method('flush');

        $handler = new AnalyzeClothingItemHandler(
            $this->clothingItemRepository,
            $this->colorRepository,
            $this->colorExtractor,
            $this->colorMatcher,
            $entityManager,
            sys_get_temp_dir()
        );

        $handler->__invoke(new AnalyzeClothingItemMessage(1));

        $this->assertEquals(ClothingItemStatus::ANALYZED, $item->getStatus());
    }

    // -------------------------------------------------------
    // Set primary color
    // -------------------------------------------------------

    public function testHandlerSetsPrimaryColor(): void
    {
        $item  = $this->makeClothingItem(1);
        $color = $this->makeColor('anthracite', '#383838');

        $this->clothingItemRepository
            ->expects($this->once())
            ->method('find')
            ->willReturn($item);

        $this->colorExtractor
            ->expects($this->once())
            ->method('extract')
            ->willReturn(['primary' => '#383838', 'secondary' => []]);

        $this->colorRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$color]);

        $this->colorMatcher
            ->expects($this->once())
            ->method('findClosest')
            ->willReturn($color);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->once())
            ->method('flush');

        $handler = new AnalyzeClothingItemHandler(
            $this->clothingItemRepository,
            $this->colorRepository,
            $this->colorExtractor,
            $this->colorMatcher,
            $entityManager,
            sys_get_temp_dir()
        );

        $handler->__invoke(new AnalyzeClothingItemMessage(1));

        $primaryColors = array_filter(
            $item->getItemColors()->toArray(),
            fn(ItemColor $ic) => $ic->isPrimary()
        );

        $this->assertCount(1, $primaryColors);
        $this->assertEquals('anthracite', array_values($primaryColors)[0]->getColor()->getName());
    }

    // -------------------------------------------------------
    // Set secondary colors
    // -------------------------------------------------------

    public function testHandlerSetsSecondaryColors(): void
    {
        $item       = $this->makeClothingItem(1);
        $primary    = $this->makeColor('anthracite', '#383838');
        $secondary  = $this->makeColor('light_gray', '#C8C8C8');

        $this->clothingItemRepository
            ->expects($this->once())
            ->method('find')
            ->willReturn($item);

        $this->colorExtractor
            ->expects($this->once())
            ->method('extract')
            ->willReturn(['primary' => '#383838', 'secondary' => ['#C8C8C8']]);

        $this->colorRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$primary, $secondary]);

        $this->colorMatcher
            ->expects($this->exactly(2))
            ->method('findClosest')
            ->willReturnOnConsecutiveCalls($primary, $secondary);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->once())
            ->method('flush');

        $handler = new AnalyzeClothingItemHandler(
            $this->clothingItemRepository,
            $this->colorRepository,
            $this->colorExtractor,
            $this->colorMatcher,
            $entityManager,
            sys_get_temp_dir()
        );

        $handler->__invoke(new AnalyzeClothingItemMessage(1));

        $this->assertCount(2, $item->getItemColors());
    }

    // -------------------------------------------------------
    // No suitable color found
    // -------------------------------------------------------

    public function testHandlerSkipsColorIfNoMatchFound(): void
    {
        $item = $this->makeClothingItem(1);

        $this->clothingItemRepository
            ->expects($this->once())
            ->method('find')
            ->willReturn($item);

        $this->colorExtractor
            ->expects($this->once())
            ->method('extract')
            ->willReturn(['primary' => '#383838', 'secondary' => []]);

        $this->colorRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $this->colorMatcher
            ->expects($this->once())
            ->method('findClosest')
            ->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->once())
            ->method('flush');

        $handler = new AnalyzeClothingItemHandler(
            $this->clothingItemRepository,
            $this->colorRepository,
            $this->colorExtractor,
            $this->colorMatcher,
            $entityManager,
            sys_get_temp_dir()
        );

        $handler->__invoke(new AnalyzeClothingItemMessage(1));

        $this->assertCount(0, $item->getItemColors());
        $this->assertEquals(ClothingItemStatus::ANALYZED, $item->getStatus());
    }
}
