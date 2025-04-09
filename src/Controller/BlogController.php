<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

/**
 * @psalm-type BlogPost = array{id: string, slug: string, title: string}
 */
#[Route('/blog')]
final class BlogController extends AbstractController
{
    /**
     * @var BlogPost[]
     */
    private const array POSTS = [
        [
            'id' => '1CMxiFfxpwLQybxYzSK412',
            'title' => 'Hello World',
            'slug' => 'hello-world',
        ],
        [
            'id' => '1CMxiFfxpwLQybxYzSNRcJ',
            'title' => 'This is another Post',
            'slug' => 'another-post',
        ],
        [
            'id' => '1CMxiFfxpwLQybxYzSPSep',
            'title' => 'This is the last example',
            'slug' => 'last-example',
        ],
    ];

    #[Route('/', name: 'blog_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return new JsonResponse(data: $this->getPostsList());
    }

    #[Route(
        '/{id<'.Requirement::UID_BASE58.'>}',
        name: 'blog_by_id',
        requirements: ['id' => Requirement::UID_BASE58],
        methods: ['GET']
    )]
    public function post(string $id): JsonResponse
    {
        try {
            $post = $this->getPostById($id);
        } catch (\OutOfBoundsException $outOfBoundsException) {
            throw $this->createNotFoundException('Post not found', $outOfBoundsException);
        }

        return new JsonResponse(data: $post);
    }

    #[Route('/{slug}', name: 'blog_by_slug', requirements: ['slug' => '[a-z0-9-]+'], methods: ['GET'])]
    public function postBySlug(string $slug): JsonResponse
    {
        try {
            $post = $this->getPostBySlug($slug);
        } catch (\OutOfBoundsException $outOfBoundsException) {
            throw $this->createNotFoundException('Post not found', $outOfBoundsException);
        }

        return new JsonResponse(data: $post);
    }

    /**
     * @return (array<string, string>[])
     * @psalm-return BlogPost[]
     */
    private function getPostsList(): array
    {
        return self::POSTS;
    }

    /**
     * @return array<string, string>
     * @psalm-return BlogPost
     *
     * @throws \OutOfBoundsException
     */
    private function getPostById(string $id): array
    {
        return $this->getPostByProperty('id', $id);
    }

    /**
     * @return array<string, string>
     * @psalm-return BlogPost
     *
     * @throws \OutOfBoundsException
     */
    private function getPostBySlug(string $slug): array
    {
        return $this->getPostByProperty('slug', $slug);
    }

    /**
     * @return array<string, string>
     * @psalm-return BlogPost
     *
     * @throws \OutOfBoundsException
     */
    private function getPostByProperty(string $property, string $value): array
    {
        /**
         * @var array<string, string>|null $post
         * @psalm-var BlogPost|null $post
         */
        $post = array_find(
            $this->getPostsList(),
            fn (array $post): bool => $post[$property] === $value
        );

        if (is_array($post)) {
            return $post;
        }

        throw new \OutOfBoundsException(sprintf('Post with %s "%s" not found', $property, $value));
    }
}
