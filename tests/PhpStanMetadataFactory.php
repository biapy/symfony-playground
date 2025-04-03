<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\Tests;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\Persistence\ObjectManager;

/**
 * @template T of ClassMetadata<object>
 *
 * @implements ClassMetadataFactory<T>
 */
final class PhpStanMetadataFactory implements ClassMetadataFactory
{
    /**
     * Last used object manager.
     */
    private ?ObjectManager $manager = null;

    /**
     * @param non-empty-list<ManagerRegistry> $doctrineRegistries
     */
    public function __construct(
        private array $doctrineRegistries,
    ) {
    }

    /**
     * @return ClassMetadata<object>[]
     * @psalm-return list<T>
     */
    #[\Override]
    public function getAllMetadata(): array
    {
        /**
         * @psalm-var list<T>
         */
        $result = array_merge(...array_map(
            fn (ObjectManager $manager): array => $manager->getMetadataFactory()->getAllMetadata(),
            array_values($this->getAllManagers()),
        ));

        return $result;
    }

    /**
     * @param class-string $className
     *
     * @return ClassMetadata<object>
     * @psalm-return T
     */
    #[\Override]
    public function getMetadataFor(string $className): ClassMetadata
    {
        $manager = $this->getManagerForClass($className);

        if (null === $manager) {
            reset($this->doctrineRegistries);
            $manager = current($this->doctrineRegistries)->getManager();
        }

        /** @psalm-var T $metadata */
        $metadata = $manager->getClassMetadata($className);

        return $metadata;
    }

    /**
     * @param class-string $className
     */
    #[\Override]
    public function isTransient(string $className): bool
    {
        $manager = $this->getManagerForClass($className);

        if (null === $manager) {
            return true;
        }

        return $manager->getMetadataFactory()->isTransient($className);
    }

    /**
     * @param class-string $className
     */
    #[\Override]
    public function hasMetadataFor(string $className): bool
    {
        $manager = $this->getManagerForClass($className);

        if (null === $manager) {
            return false;
        }

        return $manager->getMetadataFactory()->hasMetadataFor($className);
    }

    /**
     * @param class-string          $className
     * @param ClassMetadata<object> $class
     * @psalm-param T $class
     */
    #[\Override]
    public function setMetadataFor(string $className, ClassMetadata $class): void
    {
        $manager = $this->getManagerForClass($className);

        if (null === $manager) {
            throw new \RuntimeException(sprintf('No manager found for class "%s"', $className));
        }

        $manager->getMetadataFactory()->setMetadataFor($className, $class);
    }

    /**
     * @param class-string $className
     */
    public function getManagerForClass(string $className): ?ObjectManager
    {
        if (null !== $this->manager && $this->manager->getMetadataFactory()->hasMetadataFor($className)) {
            return $this->manager;
        }

        foreach ($this->doctrineRegistries as $doctrine) {
            $manager = $doctrine->getManagerForClass($className);
            if (null !== $manager) {
                $this->manager = $manager;

                return $manager;
            }
        }

        return null;
    }

    /**
     * @return array<string,ObjectManager>
     */
    private function getAllManagers(): array
    {
        return array_merge(...array_map(
            /**
             * @return array<string,ObjectManager>
             */
            fn (ManagerRegistry $registry): array => $registry->getManagers(),
            $this->doctrineRegistries,
        ));
    }
}
