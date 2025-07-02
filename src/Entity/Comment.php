<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
    ],
)]
class Comment
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV7 $id;

    public function __construct(
        #[ORM\ManyToOne(inversedBy: 'comments')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private BlogPost $blogPost,
        #[ORM\ManyToOne(inversedBy: 'comments')]
        #[ORM\JoinColumn(nullable: false)]
        private User $author,
        #[ORM\Column(type: Types::TEXT)]
        private string $content,
    ) {
        $this->id = Uuid::v7();

        $blogPost->addComment($this);
    }

    public function getId(): UuidV7
    {
        return $this->id;
    }

    public function getBlogPost(): BlogPost
    {
        return $this->blogPost;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}
