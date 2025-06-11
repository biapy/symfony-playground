<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Nepada\EmailAddress\RfcEmailAddress;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV5;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource]
class User implements PasswordAuthenticatedUserInterface, \Stringable
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private UuidV5 $id;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $displayName = null;

    #[ORM\Column(length: 128)]
    private string $passwordHash = '';

    /**
     * @var Collection<array-key, BlogPost>
     */
    #[ORM\OneToMany(targetEntity: BlogPost::class, mappedBy: 'author', cascade: ['persist'])]
    #[ORM\OrderBy(['created_at' => 'DESC'])]
    private Collection $blogPosts;

    /**
     * @var Collection<array-key, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'author', cascade: ['persist'])]
    #[ORM\OrderBy(['created_at' => 'DESC'])]
    private Collection $comments;

    public function __construct(
        #[ORM\Column(type: RfcEmailAddress::class, length: 320, unique: true)]
        private RfcEmailAddress $email,
    ) {
        $this->id = $this->createUuidV5FromEmailAddress($email);

        $this->blogPosts = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->displayName ?? $this->email->toString();
    }

    #[\Override]
    public function getPassword(): string
    {
        return $this->passwordHash;
    }

    public function getId(): UuidV5
    {
        return $this->id;
    }

    public function getDiplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function getEmail(): RfcEmailAddress
    {
        return $this->email;
    }

    public function setEmail(RfcEmailAddress $email): void
    {
        $this->email = $email;
    }

    /**
     * @return BlogPost[]
     */
    public function getBlogPosts(): array
    {
        return $this->blogPosts->toArray();
    }

    /**
     * @return Comment[]
     */
    public function getComments(): array
    {
        return $this->comments->toArray();
    }

    /**
     * Creates a UUID v5 in the URL namespace from a 'mailto:' URL containing the email address.
     */
    private function createUuidV5FromEmailAddress(RfcEmailAddress $email): UuidV5
    {
        $urlNamespace = Uuid::fromString(Uuid::NAMESPACE_URL);
        $emailUrl = sprintf('mailto:%s', urlencode($email->toString()));

        return Uuid::v5($urlNamespace, $emailUrl);
    }
}
