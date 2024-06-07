<?php

namespace App\Entity;

use Assert\Length;
use Assert\NotBlank;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Entity\MovieHasPeople;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MovieRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
#[ApiResource(
    // TODO : créer pagination et filtres
    // TODO : Créer DataProvider pour requetes n+1 si temps
    // TODO : créer doc si temps
    normalizationContext: ['groups' => ['read:Movie:collection:full']],
    operations: [
        new Get(
            normalizationContext: ['groups' => ['read:Movie:item']]
        ),
        new GetCollection(),
        new Post(
            denormalizationContext: ['groups' => ['write:Movie']],
            security: 'is_granted("ROLE_ADMIN")',
            openapiContext: [
                'security' => [['JWT' => []]]
            ]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['write:Movie']],
            security: 'is_granted("ROLE_ADMIN")',
            openapiContext: [
                'security' => [['JWT' => []]],
            ]
        ),
        new Delete(
            security: 'is_granted("ROLE_ADMIN")',
            openapiContext: [
                'security' => [['JWT' => []]]
            ]
        )
    ],
),
ApiFilter(SearchFilter::class, properties: ['title' => 'partial']),
ApiFilter(OrderFilter::class, properties: ['id', 'name'], arguments: ['orderParameterName' => 'order'] )
]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:Movie:collection:light','read:Movie:collection:full', 'read:Movie:item', 'read:People:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:Movie:collection:light','read:Movie:collection:full', 'read:Movie:item', 'write:Movie', 'read:People:item'])]
    #[Assert\NotBlank(message: "The title must not be blank.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "The title must be at maximum {{ limit }} characters long."
    )]
    private ?string $title = null;

    #[ORM\Column]
    #[Groups(['read:Movie:collection:full', 'read:Movie:item', 'write:Movie', 'read:People:item'])]
    #[Assert\NotBlank(message: "The duration must not be blank.")]
    #[Assert\Positive(message: "The duration must be a positive number.")]
    private ?int $duration = null;

    /**
     * @var Collection<int, Type>
     */
    #[ORM\ManyToMany(targetEntity: Type::class, inversedBy: 'movies', cascade: ["persist"])]
    #[ORM\JoinTable(name: 'movie_has_type')]
    #[ORM\JoinColumn(name: 'movie_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'type_id', referencedColumnName: 'id')]
    #[Groups(['read:Movie:collection:full', 'read:Movie:item','write:Movie'])]
    private Collection $types;

    /**
     * @var Collection<int, MovieHasPeople>
     */
    #[ORM\OneToMany(mappedBy: 'movie', targetEntity: MovieHasPeople::class, cascade: ["remove"])]
    #[Groups(['read:Movie:item'])]
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

    #[SerializedName('castCount')]
    #[Groups(['read:Movie:collection:full'])] // TODO : juger si réélement utile
    public function getCastCount(): int
    {
        return $this->cast->count();
    }

}
