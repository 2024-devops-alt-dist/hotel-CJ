<?php

namespace App\Entity;

use App\Repository\PictureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PictureRepository::class)]
class Picture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 850, nullable: true)]
    private ?string $path = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_principale = null;

    /**
     * @var Collection<int, Chambre>
     */
    #[ORM\ManyToMany(targetEntity: Chambre::class, mappedBy: 'images')]
    private Collection $chambres;

    public function __construct()
    {
        $this->chambres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function isPrincipale(): ?bool
    {
        return $this->is_principale;
    }

    public function setPrincipale(bool $is_principale): static
    {
        $this->is_principale = $is_principale;

        return $this;
    }

    /**
     * @return Collection<int, Chambre>
     */
    public function getChambres(): Collection
    {
        return $this->chambres;
    }

    public function addChambre(Chambre $chambre): static
    {
        if (!$this->chambres->contains($chambre)) {
            $this->chambres->add($chambre);
            $chambre->addImage($this);
        }

        return $this;
    }

    public function removeChambre(Chambre $chambre): static
    {
        if ($this->chambres->removeElement($chambre)) {
            $chambre->removeImage($this);
        }

        return $this;
    }
}
