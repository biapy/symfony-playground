<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\Tests\Integration\EventListener;

use App\DataFixtures\MyEntityFixtures;
use App\Repository\MyEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class MyEntityOnFlushListenerTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private AbstractDatabaseTool $databaseTool;

    private MyEntityRepository $myEntityRepository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $container = self::getContainer();

        /**
         * @var DatabaseToolCollection $databaseToolCollection
         *
         * @phpstan-ignore-next-line symfonyContainer.serviceNotFound
         */
        $databaseToolCollection = $container->get(DatabaseToolCollection::class);
        $this->databaseTool = $databaseToolCollection->get();

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->myEntityRepository = $container->get(MyEntityRepository::class);
    }

    #[\Override]
    protected function tearDown(): void
    {
        unset(
            $this->myEntityRepository,
            $this->entityManager,
            $this->databaseTool
        );

        parent::tearDown();
    }

    public function testMyEntityOnFlushListener(): void
    {
        $this->databaseTool->loadFixtures([
            MyEntityFixtures::class,
        ]);

        $child1 = $this->myEntityRepository->findOneByName('child1');
        $child2 = $this->myEntityRepository->findOneByName('child2');

        self::assertFalse($child1->getPath()->isDescendantOf($child2->getPath()));

        $child1->setParent($child2);

        $this->entityManager->flush();

        unset($child1, $child2);

        $this->entityManager->clear();

        $child1 = $this->myEntityRepository->findOneByName('child1');
        $child2 = $this->myEntityRepository->findOneByName('child2');
        $grandChildEntity1 = $this->myEntityRepository->findOneByName('grandchild1');
        $greatGrandChildEntity1 = $this->myEntityRepository->findOneByName('greatgrandchild1');

        self::assertTrue($greatGrandChildEntity1->getPath()->isDescendantOf($child1->getPath()));
        self::assertTrue($greatGrandChildEntity1->getPath()->isDescendantOf($child2->getPath()));
        self::assertTrue($grandChildEntity1->getPath()->isDescendantOf($child2->getPath()));
    }
}
