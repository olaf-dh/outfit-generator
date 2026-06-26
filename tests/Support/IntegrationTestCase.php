<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\DataFixtures\ColorFixtures;
use Doctrine\Bundle\FixturesBundle\Loader\SymfonyFixturesLoader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class IntegrationTestCase extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $em = self::getContainer()->get(EntityManagerInterface::class);

        self::assertInstanceOf(EntityManagerInterface::class, $em);

        $this->entityManager = $em;
    }

    protected function loadFixtures(): void
    {
        $loader = new SymfonyFixturesLoader();
        $loader->addFixture($this->getFixture());

        $purger = new ORMPurger($this->entityManager);
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }

    protected function getFixture(): FixtureInterface
    {
        $fixture = self::getContainer()->get(ColorFixtures::class);
        assert($fixture instanceof FixtureInterface);

        return $fixture;
    }
}
