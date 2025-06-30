<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BrandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BrandRepository::class)]
class Brand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Perimeter>
     */
    #[ORM\ManyToMany(targetEntity: Perimeter::class, mappedBy: 'brands')]
    private Collection $perimeters;

    public function __construct()
    {
        $this->perimeters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Perimeter>
     */
    public function getPerimeters(): Collection
    {
        return $this->perimeters;
    }

    public function addPerimeter(Perimeter $perimeter): static
    {
        if (!$this->perimeters->contains($perimeter)) {
            $this->perimeters->add($perimeter);
            $perimeter->addBrand($this);
        }

        return $this;
    }

    public function removePerimeter(Perimeter $perimeter): static
    {
        if ($this->perimeters->removeElement($perimeter)) {
            $perimeter->removeBrand($this);
        }

        return $this;
    }
}
