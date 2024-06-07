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
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: MovieHasPeopleRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            denormalizationContext: ['groups' => ['write:MovieHasPeople']],
            security: 'is_granted("ROLE_ADMIN")',
            openapiContext: [
                'security' => [['JWT' => []]],
                'requestBody' => [
                    'content' => [
                        'application/ld+json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'movie' => [
                                        'type' => 'string',
                                        'example' => '/api/movies/1'
                                    ],
                                    'people' => [
                                        'type' => 'string',
                                        'example' => '/api/peoples/1'
                                    ],
                                    'role' => [
                                        'type' => 'string',
                                        'example' => 'acteur'
                                    ],
                                    'significance' => [
                                        'type' => 'string',
                                        'example' => 'principal'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['write:MovieHasPeople']],
            security: 'is_granted("ROLE_ADMIN")',
            openapiContext: [
                'security' => [['JWT' => []]],
                'requestBody' => [
                    'content' => [
                        'application/merge-patch+json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'movie' => [
                                        'type' => 'string',
                                        'example' => '/api/movies/1'
                                    ],
                                    'people' => [
                                        'type' => 'string',
                                        'example' => '/api/peoples/1'
                                    ],
                                    'role' => [
                                        'type' => 'string',
                                        'example' => 'acteur'
                                    ],
                                    'significance' => [
                                        'type' => 'string',
                                        'example' => 'principal'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
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
    #[Assert\NotNull(message: "The people must not be null.")]
    #[Groups(['read:Movie:item','write:MovieHasPeople'])]
    private People $people;

    #[ORM\Column(name: 'role', length: 255)]
    #[Groups(['read:Movie:item','read:People:item','write:MovieHasPeople'])]
    #[Assert\NotBlank(message: "The role must not be blank.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "The role must be at maximum {{ limit }} characters long."
    )]
    private ?string $role;

    #[ORM\Column(name: 'significance', length: 255)]
    #[Groups(['read:Movie:item','read:People:item','write:MovieHasPeople'])]
    #[Assert\Choice(
        choices: ["principal", "secondaire"],
        message: "The significance must be either 'principal' or 'secondaire'."
    )]
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
