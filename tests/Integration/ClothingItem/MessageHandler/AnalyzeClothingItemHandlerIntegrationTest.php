<?php

declare(strict_types=1);

namespace App\Tests\Integration\ClothingItem\MessageHandler;

use App\ClothingItem\Enum\ClothingItemStatus;
use App\ClothingItem\Message\AnalyzeClothingItemMessage;
use App\ClothingItem\MessageHandler\AnalyzeClothingItemHandler;
use App\Color\Analyzer\ColorExtractionApiService;
use App\Color\Matcher\ColorMatchingService;
use App\Color\Resolver\ColorFamilyResolver;
use App\Color\Service\ColorConverterService;
use App\DataFixtures\ColorFixtures;
use App\Entity\ClothingItem;
use App\Entity\User;
use App\Repository\ClothingItemRepository;
use App\Repository\ColorRepository;
use App\Tests\Support\IntegrationTestCase;
use Doctrine\Bundle\FixturesBundle\Loader\SymfonyFixturesLoader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

/**
 * @group integration
 */
final class AnalyzeClothingItemHandlerIntegrationTest extends IntegrationTestCase
{
    private User $owner;

    /**
     * @var ColorExtractionApiService&MockObject $api
     */
    private ColorExtractionApiService $api;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures();
        $this->createUser();

        $this->api = $this->createMock(ColorExtractionApiService::class);
    }

    // --- Helper methods ---
    private function createUser(): void
    {
        $hasher = $this->createStub(PasswordHasherInterface::class);

        /* ---------- Owner ---------- */
        $this->owner = new User();
        $this->owner->setEmail('owner@example.com');
        $this->owner->setRoles(['ROLE_USER']);
        $this->owner->setFirstName('Owner');
        $this->owner->setPassword($hasher->hash('test1234'));
        $this->owner->setIsVerified(true);
        $this->entityManager->persist($this->owner);

        $this->entityManager->flush();
    }

    private function createItemHandler(): AnalyzeClothingItemHandler
    {
        $converter = new ColorConverterService();

        /** @var ClothingItemRepository $clothingRepo */
        $clothingRepo = self::getContainer()->get(ClothingItemRepository::class);

        /** @var ColorRepository $colorRepo */
        $colorRepo = self::getContainer()->get(ColorRepository::class);

        /** @var ColorMatchingService $colorMatcher */
        $colorMatcher = self::getContainer()->get(ColorMatchingService::class);

        /** @var ColorFamilyResolver $colorFamilyResolver */
        $colorFamilyResolver = self::getContainer()->get(ColorFamilyResolver::class);

        return new AnalyzeClothingItemHandler(
            clothingItemRepository: $clothingRepo,
            colorRepository: $colorRepo,
            colorMatcher: $colorMatcher,
            entityManager: $this->entityManager,
            apiService: $this->api,
            converter: $converter,
            colorFamilyResolver: $colorFamilyResolver,
            analysisDir: sys_get_temp_dir()
        );
    }

    // -------------------------------------------------------
    // Assign matched color
    // -------------------------------------------------------
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testAssignMatchedColor(): void
    {
        $shirt = new ClothingItem();
        $shirt->setName('White Shirt');
        $shirt->setMinLayerDepth(1);
        $shirt->setMaxLayerDepth(2);
        $shirt->setOwner($this->owner);
        $shirt->setPhotoPath('shirt.jpg');

        $this->entityManager->persist($shirt);
        $this->entityManager->flush();

        /** @var int $itemId */
        $itemId = $shirt->getId();

        $this->api
            ->expects($this->once())
            ->method('extractSingle')
            ->willReturn([['hex' => '#F8F8F8', 'weight' => 67.5]]);

        $handler = $this->createItemHandler();
        $handler(new AnalyzeClothingItemMessage($itemId));

        $this->entityManager->refresh($shirt);

        self::assertSame(ClothingItemStatus::ANALYZED, $shirt->getStatus());
        self::assertCount(1, $shirt->getItemColors());

        $itemColor = $shirt->getItemColors()->first();

        self::assertNotFalse($itemColor);
        self::assertSame('#F8F8F8', $itemColor->getColor()->getHexCode());
    }

    // -------------------------------------------------------
    // Assign multiple matched colors
    // -------------------------------------------------------
    /**
     * @throws ORMException
     */
    public function testAssignMultipleMatchedColors(): void
    {
        $pullover = new ClothingItem();
        $pullover->setName('Multicolor Pullover');
        $pullover->setMinLayerDepth(1);
        $pullover->setMaxLayerDepth(2);
        $pullover->setOwner($this->owner);
        $pullover->setPhotoPath('pullover.jpg');

        $this->entityManager->persist($pullover);
        $this->entityManager->flush();

        /** @var int $itemId */
        $itemId = $pullover->getId();

        $this->api
            ->expects($this->once())
            ->method('extractSingle')
            ->willReturn([['hex' => '#F8F8F8', 'weight' => 45.3], ['hex' => '#101010', 'weight' => 24.7]]);

        $handler = $this->createItemHandler();
        $handler(new AnalyzeClothingItemMessage($itemId));

        $this->entityManager->refresh($pullover);

        $itemColors = $pullover->getItemColors();
        self::assertCount(2, $itemColors);

        $families = [];
        foreach ($itemColors as $itemColor) {
            $families[] = $itemColor->getColor()->getFamily()?->value;
        }

        self::assertContains('white', $families);
        self::assertContains('black', $families);
    }

    // -------------------------------------------------------
    // Mark item as PENDING by exception
    // -------------------------------------------------------
    public function testMarksItemAsAnalyzedWhenApiThrowsException(): void
    {
        $stripedShirt = new ClothingItem();
        $stripedShirt->setName('Striped Button Down Shirt');
        $stripedShirt->setMinLayerDepth(2);
        $stripedShirt->setMaxLayerDepth(2);
        $stripedShirt->setOwner($this->owner);
        $stripedShirt->setPhotoPath('striped_shirt.jpg');

        $this->entityManager->persist($stripedShirt);
        $this->entityManager->flush();

        $this->api
            ->expects($this->never())
            ->method('extractSingle')
            ->willThrowException(new InvalidArgumentException('API returned error.'));

        self::assertSame(ClothingItemStatus::PENDING, $stripedShirt->getStatus());
        self::assertCount(0, $stripedShirt->getItemColors());
    }
}
