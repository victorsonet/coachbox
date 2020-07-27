<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Game
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;
    
    /**
     * @ORM\Column(type="string", unique=true)
     * @Gedmo\Slug(fields={"name"}, updatable=true)
     */
    private $slug;

    /**
     * @ORM\ManyToMany(targetEntity=Genre::class, mappedBy="games")
     */
    private $genres;

    /**
     * @ORM\ManyToMany(targetEntity=Coach::class, inversedBy="games")
     */
    private $coaches;

    public function __construct()
    {
        $this->genres = new ArrayCollection();
        $this->coaches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Genre[]
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): self
    {
        if (!$this->genres->contains($genre)) {
            $this->genres[] = $genre;
            $genre->addGame($this);
        }

        return $this;
    }

    public function removeGenre(Genre $genre): self
    {
        if ($this->genres->contains($genre)) {
            $this->genres->removeElement($genre);
            $genre->removeGame($this);
        }

        return $this;
    }

    /**
     * @return Collection|Coach[]
     */
    public function getCoaches(): Collection
    {
        return $this->coaches;
    }

    public function addCoach(Coach $coach): self
    {
        if (!$this->coaches->contains($coach)) {
            $this->coaches[] = $coach;
        }

        return $this;
    }

    public function removeCoach(Coach $coach): self
    {
        if ($this->coaches->contains($coach)) {
            $this->coaches->removeElement($coach);
        }

        return $this;
    }

}
