<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\MyEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MyEntityFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $rootEntity = new MyEntity('root');
        $manager->persist($rootEntity);
        $this->addReference($rootEntity->getName(), $rootEntity);

        $childEntity1 = new MyEntity('child1', $rootEntity);
        $manager->persist($childEntity1);

        $childEntity2 = new MyEntity('child2', $rootEntity);
        $manager->persist($childEntity2);

        $childEntity3 = new MyEntity('child3', $rootEntity);
        $manager->persist($childEntity3);

        $grandChildEntity1 = new MyEntity('grandchild1', $childEntity1);
        $manager->persist($grandChildEntity1);

        $greatGrandChildEntity1 = new MyEntity('greatgrandchild1', $grandChildEntity1);
        $manager->persist($greatGrandChildEntity1);

        $manager->flush();
    }
}
