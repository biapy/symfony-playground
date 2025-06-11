<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Nepada\EmailAddress\RfcEmailAddress;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $manager->flush();
    }

    private function loadBlogPosts(ObjectManager $manager): void
    {
        $anonymousUser = $this->getReference('anonymous_user', User::class);

        $blogPost = new BlogPost(
            title: 'A first post!',
            content: 'Post text!',
            author: $anonymousUser,
        );
        $blogPost->setPublished(new \DateTimeImmutable('2025-04-01 12:00:00'));

        $manager->persist($blogPost);

        $blogPost = new BlogPost(
            title: 'A second post!',
            content: 'Post text!',
            author: $anonymousUser,
        );
        $blogPost->setPublished(new \DateTimeImmutable('2025-04-02 12:00:00'));

        $manager->persist($blogPost);
    }

    private function loadUsers(ObjectManager $manager): void
    {
        $anonymousUser = new User(
            email: $this->createRfcEmailAddressFromString('none@none.localhost'),
        );
        $anonymousUser->setDisplayName('None');

        $this->addReference('anonymous_user', $anonymousUser);

        $manager->persist($anonymousUser);

        $adminUser = new User(
            email: $this->createRfcEmailAddressFromString('admin@blog.com'),
        );
        $passwordHash = $this->passwordHasher->hashPassword(
            $adminUser,
            'admin@blog.com',
        );
        $adminUser->setPasswordHash($passwordHash);
        $adminUser->setDisplayName('Admin');

        $this->addReference('admin_user', $adminUser);

        $manager->persist($adminUser);
    }

    /**
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    private function createRfcEmailAddressFromString(string $email): RfcEmailAddress
    {
        return RfcEmailAddress::fromString($email);
    }
}
