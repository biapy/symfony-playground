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
use Symfony\Component\Uid\Uuid;

/**
 * @psalm-type BlogPost = array{id: string, slug: string, title: string}
 */
#[Route('/blog')]
final class BlogController extends AbstractController
{
    public const int POSTS_PER_PAGE = 2;

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

    #[Route(
        '/{page?}',
        name: 'blog_list',
        defaults: ['page' => 1],
        requirements: ['page' => '\d+'],
        methods: ['GET']
    )]
    public function list(int $page = 1): JsonResponse
    {
        try {
            $pagePostsUrls = $this->getPostsUniqueUrlsForPage($page);
        } catch (\OutOfBoundsException|\InvalidArgumentException $exception) {
            throw $this->createNotFoundException('Page not found', $exception);
        }

        $data = [
            'page' => $page,
            'data' => $pagePostsUrls,
        ];

        return new JsonResponse(data: $data);
    }

    #[Route(
        '/post/{id<'.Requirement::UID_BASE58.'>}',
        name: 'blog_by_id',
        requirements: ['id' => Requirement::UID_BASE58],
        methods: ['GET']
    )]
    public function post(Uuid $id): JsonResponse
    {
        try {
            $post = $this->getPostById($id);
        } catch (\OutOfBoundsException $outOfBoundsException) {
            throw $this->createNotFoundException('Post not found', $outOfBoundsException);
        }

        return new JsonResponse(data: $post);
    }

    #[Route('/post/{slug}', name: 'blog_by_slug', requirements: ['slug' => '[a-z0-9-]+'], methods: ['GET'])]
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
     * @return string[]
     */
    private function getPostsUniqueUrlsForPage(int $page): array
    {
        $posts = $this->getPostsForPage($page);

        return $this->getPostsUniqueUrls($posts);
    }

    /**
     * @param (array<string, string>[]) $posts
     * @psalm-param BlogPost[] $posts
     *
     * @return string[]
     */
    private function getPostsUniqueUrls(array $posts): array
    {
        return array_map($this->getPostUniqueUrl(...), $posts);
    }

    /**
     * @param array<string, string> $post
     * @psalm-param BlogPost $post
     */
    private function getPostUniqueUrl(array $post): string
    {
        return $this->generateUrl('blog_by_id', ['id' => $post['id']]);
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
     * @return (array<string, string>[])
     * @psalm-return BlogPost[]
     *
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     */
    private function getPostsForPage(int $page): array
    {
        if ($page < 1) {
            throw new \InvalidArgumentException('Page must be greater than 0');
        }

        $posts = $this->getPostsList();
        $posts = array_chunk($posts, self::POSTS_PER_PAGE, true);

        $pagePosts = $posts[$page - 1] ?? false;

        if (false === $pagePosts) {
            throw new \OutOfBoundsException('Page not found');
        }

        return $pagePosts;
    }

    /**
     * @return array<string, string>
     * @psalm-return BlogPost
     *
     * @throws \OutOfBoundsException
     */
    private function getPostById(Uuid $id): array
    {
        return $this->getPostByProperty('id', $id->toBase58());
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
