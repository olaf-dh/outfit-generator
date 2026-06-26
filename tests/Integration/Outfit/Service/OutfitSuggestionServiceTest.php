<?php

declare(strict_types=1);

namespace App\Tests\Integration\Outfit\Service;

use App\ClothingItem\Enum\BodyZone;
use App\ClothingItem\Enum\SeasonType;
use App\ClothingItem\Enum\StyleType;
use App\ClothingItem\Enum\WeatherConditionType;
use App\DataFixtures\CategoryFixtures;
use App\DataFixtures\ClothingItemFixtures;
use App\DataFixtures\ColorFixtures;
use App\DataFixtures\LookupFixtures;
use App\DataFixtures\MaterialFixtures;
use App\DataFixtures\SubCategoryFixtures;
use App\DTO\Outfit\OutfitSuggestion;
use App\Entity\ClothingItem;
use App\Outfit\Service\OutfitSuggestionService;
use Doctrine\Bundle\FixturesBundle\Loader\SymfonyFixturesLoader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group integration
 */
class OutfitSuggestionServiceTest extends KernelTestCase
{
    private OutfitSuggestionService $service;
    private EntityManagerInterface $entityManager;

    /**
     * Fixture references after loading
     *
     * @var array<string, ReferenceRepository>
     */
    private array $references = [];

    protected function setUp(): void
    {
        self::bootKernel();

        /** @var EntityManagerInterface $entityManager */
        $entityManager       = static::getContainer()->get(EntityManagerInterface::class);
        $this->entityManager = $entityManager;

        /** @var OutfitSuggestionService $service */
        $service             = static::getContainer()->get(OutfitSuggestionService::class);
        $this->service       = $service;

        $this->loadFixtures();
    }

    private function loadFixtures(): void
    {
        $loader = new SymfonyFixturesLoader();

        $loader->addFixture($this->getFixture(CategoryFixtures::class));
        $loader->addFixture($this->getFixture(SubCategoryFixtures::class));
        $loader->addFixture($this->getFixture(MaterialFixtures::class));
        $loader->addFixture($this->getFixture(ColorFixtures::class));
        $loader->addFixture($this->getFixture(LookupFixtures::class));
        $loader->addFixture($this->getFixture(ClothingItemFixtures::class));

        $purger   = new ORMPurger($this->entityManager);
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->execute($loader->getFixtures());

        // Save references after loading
        $this->references['items'] = $executor->getReferenceRepository();
    }

    private function getFixture(string $fixtureClass): FixtureInterface
    {
        $fixture = static::getContainer()->get($fixtureClass);
        assert($fixture instanceof FixtureInterface);

        return $fixture;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    private function getItem(string $reference): ClothingItem
    {
        /** @var ClothingItem $item */
        $item = $this->references['items']->getReference($reference, ClothingItem::class);

        // Fresh object from DB to load all relations
        $item = $this->entityManager->find(ClothingItem::class, $item->getId());
        self::assertNotNull($item, sprintf('Item "%s" not found in DB', $reference));

        return $item;
    }

    // -------------------------------------------------------
    // Basic return
    // -------------------------------------------------------

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testSuggestReturnsArrayOfOutfitSuggestions(): void
    {
        $pullover = $this->getItem(ClothingItemFixtures::RED_PULLOVER);

        $suggestions = $this->service->suggest(
            [$pullover],
            StyleType::SMART_CASUAL,
            SeasonType::AUTUMN
        );

        $this->assertCount(2, $suggestions);
        $this->assertSame('casual', $suggestions[0]->getItems()[0]->getStyles()[0]?->getType()->value);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testSuggestReturnsMaxTwoSuggestions(): void
    {
        $pullover = $this->getItem(ClothingItemFixtures::RED_PULLOVER);

        $suggestions = $this->service->suggest(
            [$pullover],
            StyleType::SMART_CASUAL,
            SeasonType::AUTUMN
        );

        $this->assertLessThanOrEqual(2, count($suggestions));
    }

    // -------------------------------------------------------
    // Seed-Items are always included in suggestions
    // -------------------------------------------------------
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testSeedItemsAreAlwaysIncludedInSuggestion(): void
    {
        $pullover = $this->getItem(ClothingItemFixtures::RED_PULLOVER);
        $shoes    = $this->getItem(ClothingItemFixtures::CARAMEL_SHOES);

        $suggestions = $this->service->suggest(
            [$pullover, $shoes],
            StyleType::SMART_CASUAL,
            SeasonType::AUTUMN
        );

        foreach ($suggestions as $suggestion) {
            $itemIds = array_map(
                fn(ClothingItem $item) => $item->getId(),
                $suggestion->getItems()
            );

            $this->assertContains($pullover->getId(), $itemIds);
            $this->assertContains($shoes->getId(), $itemIds);
        }
    }

    // -------------------------------------------------------
    // Mandatory zones are covered
    // -------------------------------------------------------
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testSuggestionCoversRequiredZones(): void
    {
        $pullover = $this->getItem(ClothingItemFixtures::RED_PULLOVER);

        $suggestions = $this->service->suggest(
            [$pullover],
            StyleType::SMART_CASUAL,
            SeasonType::AUTUMN
        );

        $this->assertNotEmpty($suggestions);

        foreach ($suggestions as $suggestion) {
            $zones = array_map(
                fn(ClothingItem $item) => $item->getSubCategory()?->getCategory()?->getBodyZone(),
                $suggestion->getItems()
            );

            $this->assertContains(BodyZone::UPPER_BODY, $zones);
            $this->assertContains(BodyZone::LOWER_BODY, $zones);
            $this->assertContains(BodyZone::FOOTWEAR, $zones);
        }
    }

    // -------------------------------------------------------
    // Saison will be respected
    // -------------------------------------------------------
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testSuggestionRespectsSeasonFilter(): void
    {
        $pullover = $this->getItem(ClothingItemFixtures::RED_PULLOVER);

        $suggestions = $this->service->suggest(
            [$pullover],
            StyleType::CASUAL,
            SeasonType::SUMMER  // Pullover is only for Autumn/Winter – should be empty
        );

        // Red pullover does not fit summer season → no suggestions
        $this->assertEmpty($suggestions);
    }

    // -------------------------------------------------------
    // Style will be respected
    // -------------------------------------------------------
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testSuggestionRespectsStyleFilter(): void
    {
        $sneakers = $this->getItem(ClothingItemFixtures::WHITE_SNEAKERS);

        $suggestions = $this->service->suggest(
            [$sneakers],
            StyleType::BUSINESS, // Sneaker are only Casual → incompatible
            SeasonType::SPRING
        );

        $this->assertEmpty($suggestions);
    }

    // -------------------------------------------------------
    // Weather will be respected
    // -------------------------------------------------------
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testSuggestionIncludesWeatherCompatibleItems(): void
    {
        $pullover = $this->getItem(ClothingItemFixtures::RED_PULLOVER);

        $suggestions = $this->service->suggest(
            [$pullover],
            StyleType::SMART_CASUAL,
            SeasonType::AUTUMN,
            WeatherConditionType::RAINY
        );

        // When the weather is Rainy, the trenchcoat should be suggested
        if (!empty($suggestions)) {
            $allItems = array_merge(...array_map(
                fn(OutfitSuggestion $s) => $s->getItems(),
                $suggestions
            ));

            $itemNames = array_map(
                fn(ClothingItem $item) => $item->getName(),
                $allItems
            );

            $this->assertTrue(in_array('Beige Trenchcoat', $itemNames) || in_array('Gray Wool Coat', $itemNames));
        }
    }

    // -------------------------------------------------------
    // Two suggestions are different
    // -------------------------------------------------------
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function testTwoSuggestionsAreDifferent(): void
    {
        $pullover = $this->getItem(ClothingItemFixtures::RED_PULLOVER);

        $suggestions = $this->service->suggest(
            [$pullover],
            StyleType::SMART_CASUAL,
            SeasonType::AUTUMN,
            null,
            2
        );

        if (count($suggestions) === 2) {
            $ids0 = array_map(fn($i) => $i->getId(), $suggestions[0]->getItems());
            $ids1 = array_map(fn($i) => $i->getId(), $suggestions[1]->getItems());

            sort($ids0);
            sort($ids1);

            $this->assertNotEquals($ids0, $ids1);
        } else {
            $this->markTestSkipped('Not enough clothing items for two different suggestions.');
        }
    }

    public function testReasonIsAddedOnlyOnce(): void
    {
        $suggestion = new OutfitSuggestion();

        $suggestion
            ->addReason('material.good')
            ->addReason('material.good');

        self::assertCount(1, $suggestion->getReasons());
    }

    public function testAddReason(): void
    {
        $suggestions = new OutfitSuggestion();

        $suggestions->addReason('test');

        self::assertSame('test', $suggestions->getReasons()[0]);
    }
}
