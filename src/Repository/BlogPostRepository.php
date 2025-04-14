<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\BlogPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<BlogPost>
 */
final class BlogPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPost::class);
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findOneById(Uuid $id): BlogPost
    {
        $qb = $this->createQueryBuilder('b');
        $qb->where('b.id = :id');
        $qb->setParameter('id', $id, UuidType::NAME);

        /** @var BlogPost $result */
        $result = $qb->getQuery()->getSingleResult();

        return $result;
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findOneBySlug(string $slug): BlogPost
    {
        $qb = $this->createQueryBuilder('b');
        $qb->where('b.slug = :slug');
        $qb->setParameter('slug', $slug);

        /** @var BlogPost $result */
        $result = $qb->getQuery()->getSingleResult();

        return $result;
    }
}
