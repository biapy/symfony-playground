<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\Security\Traits;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait SecurityAwareTrait
{
    private ?Security $security = null;

    private ?UserRepository $userRepository = null;

    /**
     * @internal
     */
    #[Required]
    public function setSecurity(Security $security): void
    {
        $this->security = $security;
    }

    /**
     * @internal
     */
    #[Required]
    public function setUserRepository(UserRepository $userRepository): void
    {
        $this->userRepository = $userRepository;
    }

    private function getAuthenticatedUserEntity(): User
    {
        $securityUser = $this->getSecurity()->getUser();

        if ($securityUser instanceof User) {
            return $securityUser;
        }

        if ($securityUser instanceof UserInterface) {
            return $this->getUserEntity($securityUser);
        }

        throw new AccessDeniedException('User is not authenticated.');
    }

    private function getUserEntity(UserInterface $securityUser): User
    {
        try {
            return $this->getUserRepository()->findOneByUserInterface($securityUser);
        } catch (NoResultException $noResultException) {
            throw new UserNotFoundException('User not found.', $noResultException->getCode(), $noResultException);
        }
    }

    private function getUserRepository(): UserRepository
    {
        if ($this->userRepository instanceof UserRepository) {
            return $this->userRepository;
        }

        throw new \RuntimeException('User repository not set.');
    }

    private function getSecurity(): Security
    {
        if ($this->security instanceof Security) {
            return $this->security;
        }

        throw new \RuntimeException('Security service not set.');
    }
}
