<?php

/**
 * @copyright 2025 Biapy
 * @license MIT
 */

declare(strict_types=1);

namespace App\Entity;

use App\EventListener\MyEntityEntityListener;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Ltree;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\LtreeInterface;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

/**
 * Manually edit `my_entity_path_gist_idx` in migration to use GIST.
 * Declaring the index using Doctrine attributes prevents its removal during migrations.
 */
#[ORM\Entity()]
#[ORM\Index(columns: ['path'], name: 'my_entity_path_gist_idx')]
#[ORM\EntityListeners([MyEntityEntityListener::class])]
class MyEntity implements \Stringable
{
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Id()]
    private Uuid $id;

    #[ORM\Column(type: 'ltree', unique: true)]
    private LtreeInterface $path;

    /**
     * @var Collection<array-key,MyEntity> $children
     */
    #[ORM\OneToMany(targetEntity: MyEntity::class, mappedBy: 'parent')]
    private Collection $children;

    /**
     * @SuppressWarnings("PHPMD.StaticAccess")
     */
    public function __construct(
        #[ORM\Column(unique: true, length: 128)]
        private string $name,

        #[ORM\ManyToOne(targetEntity: MyEntity::class, inversedBy: 'children')]
        private ?MyEntity $parent,
    ) {
        $this->id = Uuid::v7();
        $this->children = new ArrayCollection();

        $this->path = Ltree::fromString($this->id->toBase58());
        if ($parent instanceof MyEntity) {
            // Initialize the path using the parent.
            $this->setParent($parent);
        }
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getParent(): ?MyEntity
    {
        return $this->parent;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): LtreeInterface
    {
        return $this->path;
    }

    /**
     * @return Collection<array-key,MyEntity>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setParent(MyEntity $parent): void
    {
        if ($parent->getId()->equals($this->id)) {
            throw new \InvalidArgumentException("Parent MyEntity can't be self");
        }

        // Prevent cycles: the parent can't be a descendant of the current node.
        if ($parent->getPath()->isDescendantOf($this->getPath())) {
            throw new \InvalidArgumentException("Parent MyEntity can't be a descendant of the current MyEntity");
        }

        $this->parent = $parent;

        // Use withLeaf() to create a new Ltree instance
        // with the parent's path and the current entity's ID.
        $this->path = $parent->getPath()->withLeaf($this->id->toBase58());
    }
}
