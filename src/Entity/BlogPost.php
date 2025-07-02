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
use App\Repository\BlogPostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

#[ORM\Entity(repositoryClass: BlogPostRepository::class)]
#[Gedmo\Loggable]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
    ],
)]
class BlogPost implements \Stringable
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV7 $id;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    #[Gedmo\Slug(fields: ['title'], unique: true)]
    private ?string $slug = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $published = null;

    /**
     * @var Collection<array-key, Comment>
     */
    #[ORM\OneToMany(
        targetEntity: Comment::class,
        mappedBy: 'blogPost',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $comments;

    public function __construct(
        #[ORM\Column(length: 255)]
        private string $title,

        #[ORM\Column(type: Types::TEXT)]
        private string $content,

        #[ORM\ManyToOne(inversedBy: 'blogPosts')]
        #[ORM\JoinColumn(nullable: false, options: ['default' => 'df084faf-ce65-5fda-b342-3475a281ee37'])]
        private User $author,
    ) {
        $this->id = Uuid::v7();

        $this->comments = new ArrayCollection();
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->title;
    }

    public function getId(): UuidV7
    {
        return $this->id;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function getPublished(): ?\DateTimeImmutable
    {
        return $this->published;
    }

    public function setPublished(?\DateTimeImmutable $published): void
    {
        $this->published = $published;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return Comment[]
     */
    public function getComments(): array
    {
        return $this->comments->toArray();
    }

    public function addComment(Comment $comment): void
    {
        if ($comment->getBlogPost() !== $this) {
            throw new \LogicException('Comment does not belong to this blog post');
        }

        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
        }
    }
}
