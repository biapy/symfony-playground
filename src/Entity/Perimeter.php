<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PerimeterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PerimeterRepository::class)]
class Perimeter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Brand>
     */
    #[ORM\ManyToMany(targetEntity: Brand::class, inversedBy: 'perimeters')]
    private Collection $brands;

    public function __construct()
    {
        $this->brands = new ArrayCollection();
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
     * @return Collection<int, Brand>
     */
    public function getBrands(): Collection
    {
        return $this->brands;
    }

    public function addBrand(Brand $brand): static
    {
        if (!$this->brands->contains($brand)) {
            $this->brands->add($brand);
        }

        return $this;
    }

    public function removeBrand(Brand $brand): static
    {
        $this->brands->removeElement($brand);

        return $this;
    }
}
