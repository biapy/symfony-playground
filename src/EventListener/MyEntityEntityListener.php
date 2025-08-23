<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\MyEntity;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;

#[AsEntityListener(event: Events::onFlush, method: 'onFlush', entity: MyEntity::class)]
final readonly class MyEntityEntityListener
{
    public function onFlush(MyEntity $entity, OnFlushEventArgs $eventArgs): void
    {
        $entityManager = $eventArgs->getObjectManager();
        $unitOfWork = $entityManager->getUnitOfWork();
        $entityMetadata = $entityManager->getClassMetadata(MyEntity::class);

        $originalEntityData = $unitOfWork->getOriginalEntityData($entity);

        // check if $entity->path has changed
        // If the path stays the same, no need to update children
        if ($originalEntityData['path'] === $entity->getPath()) {
            return;
        }

        $this->updateChildrenPaths($entity, $entityMetadata, $unitOfWork);
    }

    /**
     * @param ClassMetadata<MyEntity> $entityMetadata
     */
    private function updateChildrenPaths(MyEntity $entity, ClassMetadata $entityMetadata, UnitOfWork $unitOfWork): void
    {
        foreach ($entity->getChildren() as $child) {
            // call the setParent method on the child, which recomputes its Ltree path.
            $child->setParent($entity);

            $unitOfWork->recomputeSingleEntityChangeSet($entityMetadata, $child);

            // cascade the update to the child's children
            $this->updateChildrenPaths($child, $entityMetadata, $unitOfWork);
        }
    }
}
