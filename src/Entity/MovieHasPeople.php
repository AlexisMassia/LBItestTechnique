<?php

namespace App\Entity;

use App\Entity\Movie;
use App\Entity\People;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\MovieHasPeopleRepository;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: MovieHasPeopleRepository::class)]
#[ApiResource(
    // TODO : créer doc si temps
    operations: [
        new Post(
            denormalizationContext: ['groups' => ['write:MovieHasPeople']],
            security: 'is_granted("ROLE_ADMIN")',
            openapiContext: [
                'security' => [['JWT' => []]]
            ]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['write:MovieHasPeople']],
            security: 'is_granted("ROLE_ADMIN")',
            openapiContext: [
                'security' => [['JWT' => []]]
            ]
        ),
        new Delete(
            security: 'is_granted("ROLE_ADMIN")',
            openapiContext: [
                'security' => [['JWT' => []]]
            ]
        ),
    ],
    shortName: "Movie Participant",
    description: "Represents the relationship between a movie and a person with a specific role."
)]
class MovieHasPeople
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:Movie:item','read:People:item'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'cast', targetEntity: Movie::class)]
    #[ORM\JoinColumn(name: 'Movie_id', referencedColumnName: 'id')]
    #[Assert\NotNull(message: "The movie must not be null.")]
    #[Groups(['write:MovieHasPeople'])]
    private Movie $movie;

    #[ORM\ManyToOne(inversedBy: 'movieRoles', targetEntity: People::class)]
    #[ORM\JoinColumn(name: 'People_id', referencedColumnName: 'id')]
    #[Groups(['read:Movie:item','write:MovieHasPeople'])]
    private People $people;

    #[ORM\Column(name: 'role', length: 255)]
    #[Groups(['read:Movie:item','read:People:item','write:MovieHasPeople'])]
    private ?string $role;

    #[ORM\Column(name: 'significance', length: 255)]
    // TODO ENUM
    #[Groups(['read:Movie:item','read:People:item','write:MovieHasPeople'])]
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
