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

final class AppFixtures extends Fixture
{
    /**
     * {@inheritDoc}
     *
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $anonymousUser = new User(
            email: RfcEmailAddress::fromString('none@none.localhost'),
            passwordHash: '',
        );
        $anonymousUser->setDisplayName('None');

        $manager->persist($anonymousUser);

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

        $manager->flush();
    }
}
