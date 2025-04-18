<?php

declare(strict_types=1);

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

namespace App\DataFixtures;

use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $blogPost = new BlogPost(
            title: 'A first post!',
            content: 'Post text!',
        );
        $blogPost->setPublished(new \DateTimeImmutable('2025-04-01 12:00:00'));

        $manager->persist($blogPost);

        $blogPost = new BlogPost(
            title: 'A second post!',
            content: 'Post text!',
        );
        $blogPost->setPublished(new \DateTimeImmutable('2025-04-02 12:00:00'));

        $manager->persist($blogPost);

        $manager->flush();
    }
}
