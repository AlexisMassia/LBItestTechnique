<?php

namespace App\Entity;

use App\Entity\MovieHasPeople;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MovieRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?int $duration = null;

    /**
     * @var Collection<int, Type>
     */
    #[ORM\ManyToMany(targetEntity: Type::class, inversedBy: 'movies', cascade: ["persist"])]
    #[ORM\JoinTable(name: 'movie_has_type')]
    #[ORM\JoinColumn(name: 'movie_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'type_id', referencedColumnName: 'id')]
    private Collection $types;

    /**
     * @var Collection<int, MovieHasPeople>
     */
    #[ORM\OneToMany(mappedBy: 'movie', targetEntity: MovieHasPeople::class, cascade: ["remove"])]
    private Collection $cast;

    public function __construct()
    {
        $this->types = new ArrayCollection();
        $this->cast = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return Collection<int, Type>
     */
    public function getTypes(): Collection
    {
        return $this->types;
    }

    public function addType(Type $type): static
    {
        if (!$this->types->contains($type)) {
            $this->types->add($type);
            $type->addMovie($this);
        }

        return $this;
    }

    public function removeType(Type $type): static
    {
        if ($this->types->removeElement($type)) {
            $type->removeMovie($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, MovieHasPeople>
     */
    public function getCast(): Collection
    {
        return $this->cast;
    }

    public function addCast(MovieHasPeople $movieHasPeople): static
    {
        if (!$this->cast->contains($movieHasPeople)) {
            $this->cast->add($movieHasPeople);
            $movieHasPeople->setMovie($this);
        }

        return $this;
    }

    public function removeCast(MovieHasPeople $movieHasPeople): static
    {
        if ($this->cast->removeElement($movieHasPeople)) {
            if ($movieHasPeople->getMovie() === $this) {
                $movieHasPeople->setMovie(null);
            }
        }

        return $this;
    }

}
