<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/blog')]
final class BlogController extends AbstractController
{
    public function __construct(
        private readonly BlogPostRepository $blogPostRepository,
        #[Autowire('%app.blog.posts_per_page%')]
        private readonly int $postPerPage,
    ) {
    }

    #[Route(
        '/{page?}',
        name: 'blog_list',
        defaults: ['page' => 1],
        requirements: ['page' => '\d+'],
        methods: ['GET'],
        format: 'json',
    )]
    public function list(Request $request, int $page = 1): JsonResponse
    {
        $limit = (int) $request->query->get('limit', (string) $this->postPerPage);

        if ($limit < 1) {
            throw $this->createNotFoundException('Limit must be greater than 0');
        }

        try {
            $pagePostsUrls = $this->getPostsUniqueUrlsForPage($page, $limit);
        } catch (\OutOfBoundsException $outOfBoundsException) {
            throw $this->createNotFoundException('Page not found', previous: $outOfBoundsException);
        } catch (\InvalidArgumentException $invalidArgumentException) {
            throw $this->createNotFoundException($invalidArgumentException->getMessage(), previous: $invalidArgumentException);
        }

        $data = [
            'page' => $page,
            'limit' => $limit,
            'data' => $pagePostsUrls,
        ];

        return $this->json(data: $data);
    }

    #[Route(
        '/post/{id<'.Requirement::UID_BASE58.'>}',
        name: 'blog_by_id',
        requirements: ['id' => Requirement::UID_BASE58],
        methods: ['GET'],
        format: 'json',
    )]
    public function post(
        #[MapEntity(message: 'Post not found')]
        BlogPost $post,
    ): JsonResponse {
        return $this->json(data: $post);
    }

    #[Route(
        '/post/{slug:post}',
        name: 'blog_by_slug',
        requirements: ['slug' => '[a-z0-9-]+'],
        methods: ['GET'],
        format: 'json',
    )]
    public function postBySlug(
        #[MapEntity(message: 'Post not found')]
        BlogPost $post,
    ): JsonResponse {
        return $this->json(data: $post);
    }

    #[Route('/add', name: 'blog_add', methods: ['POST'], format: 'json')]
    public function add(
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        Request $request,
    ): JsonResponse {
        $json = $request->getContent();
        $blogPost = $serializer->deserialize($json, BlogPost::class, 'json');

        $entityManager->persist($blogPost);
        $entityManager->flush();

        return $this->json($blogPost, Response::HTTP_CREATED);
    }

    /**
     * @return string[]
     */
    private function getPostsUniqueUrlsForPage(int $page, ?int $limit = null): array
    {
        $posts = $this->getPostsForPage($page, $limit);

        return $this->getPostsUniqueUrls($posts);
    }

    /**
     * @param BlogPost[] $posts
     *
     * @return string[]
     */
    private function getPostsUniqueUrls(array $posts): array
    {
        return array_map($this->getPostUniqueUrl(...), $posts);
    }

    private function getPostUniqueUrl(BlogPost $post): string
    {
        return $this->generateUrl('blog_by_id', ['id' => $post->getId()->toBase58()]);
    }

    /**
     * @return (array<string, string>[])
     * @psalm-return BlogPost[]
     *
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     */
    private function getPostsForPage(int $page, ?int $limit = null): array
    {
        $limit ??= $this->postPerPage;

        if ($page < 1) {
            throw new \InvalidArgumentException('Page must be greater than 0');
        }

        if ($limit < 1) {
            throw new \InvalidArgumentException('Limit must be greater than 0');
        }

        $pagePosts = $this->blogPostRepository->findByPage($page, $limit);

        if ([] === $pagePosts) {
            throw new \OutOfBoundsException('Page not found');
        }

        return $pagePosts;
    }
}
