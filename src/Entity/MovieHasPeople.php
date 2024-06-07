<?php

namespace App\Entity;

use App\Entity\Movie;
use App\Entity\People;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MovieHasPeopleRepository;

#[ORM\Entity(repositoryClass: MovieHasPeopleRepository::class)]
class MovieHasPeople
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'cast', targetEntity: Movie::class)]
    #[ORM\JoinColumn(name: 'Movie_id', referencedColumnName: 'id')]
    private Movie $movie;

    #[ORM\ManyToOne(inversedBy: 'movieRoles', targetEntity: People::class)]
    #[ORM\JoinColumn(name: 'People_id', referencedColumnName: 'id')]
    private People $people;

    #[ORM\Column(name: 'role', length: 255)]
    private ?string $role;

    #[ORM\Column(name: 'significance', length: 255)]
    private $significance;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): self
    {
        $this->movie = $movie;
        return $this;
    }

    public function getPeople(): ?People
    {
        return $this->people;
    }

    public function setPeople(?People $people): self
    {
        $this->people = $people;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getSignificance(): ?string
    {
        return $this->significance;
    }

    public function setSignificance(string $significance): self
    {
        $this->significance = $significance;
        return $this;
    }

}
