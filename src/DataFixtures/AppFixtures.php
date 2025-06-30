<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Nepada\EmailAddress\RfcEmailAddress;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AppFixtures extends Fixture
{
    private readonly Faker\Generator $faker;

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        $this->faker = Faker\Factory::create();
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

        foreach (range(1, 100) as $i) {
            $blogPost = $this->createFakeBlogPost($anonymousUser);
            $this->addCommentsToBlogPost($blogPost);
            $manager->persist($blogPost);

            $this->setReference(sprintf('blog_post_%d', $i), $blogPost);
        }
    }

    private function createFakeBlogPost(User $author): BlogPost
    {
        $blogPost = new BlogPost(
            title: $this->faker->sentence(),
            content: $this->faker->realText(),
            author: $author,
        );
        $blogPost->setPublished(
            \DateTimeImmutable::createFromInterface($this->faker->dateTimeThisYear())
        );

        return $blogPost;
    }

    private function addCommentsToBlogPost(BlogPost $blogPost): void
    {
        $anonymousUser = $this->getReference('anonymous_user', User::class);

        foreach (range(1, $this->faker->randomDigitNotNull()) as $i) {
            $comment = new Comment(
                blogPost: $blogPost,
                author: $anonymousUser,
                content: $this->faker->paragraph(),
            );

            $comment->setCreatedAt($this->faker->dateTimeThisYear());
        }
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
