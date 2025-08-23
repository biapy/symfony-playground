<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\MyEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\LtreeInterface;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<MyEntity>
 */
final class MyEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MyEntity::class);
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findOneById(Uuid $id): MyEntity
    {
        $query = $this->createQueryBuilder('e')
            ->andWhere('e.id = :id')
            ->setParameter('id', $id, UuidType::NAME)
            ->getQuery();

        /** @var MyEntity $entity */
        $entity = $query->getSingleResult();

        return $entity;
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findOneByPath(LtreeInterface $path): MyEntity
    {
        $query = $this->createQueryBuilder('e')
            ->andWhere('e.path = :path')
            ->setParameter('path', $path, 'ltree')
            ->getQuery();

        /** @var MyEntity $entity */
        $entity = $query->getSingleResult();

        return $entity;
    }
}
