<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Nepada\EmailAddress\RfcEmailAddress;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
final class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneByUserInterface(UserInterface $user): User
    {
        return $this->findOneByEmail($user->getUserIdentifier());
    }

    /**
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function findOneByEmail(string $email): User
    {
        $emailAddress = RfcEmailAddress::fromString($email);

        return $this->findOneByEmailAddress($emailAddress);
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findOneByEmailAddress(RfcEmailAddress $emailAddress): User
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $emailAddress, RfcEmailAddress::class);

        /** @var User $user */
        $user = $queryBuilder->getQuery()->getSingleResult();

        return $user;
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
