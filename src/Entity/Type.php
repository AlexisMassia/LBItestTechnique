<?php

namespace App\Entity;

use App\Entity\Movie;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TypeRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TypeRepository::class)]
#[ApiResource(
    // TODO : créer pagination et filtres
    // TODO : Créer DataProvider pour requetes n+1 si temps
    // TODO : créer doc si temps    paginationItemsPerPage: 20,
    normalizationContext: ['groups' => ['read:Type:collection']],
    operations: [
        new Get(normalizationContext: ['groups' => ['read:Type:item', 'read:Movie:collection:light']]),
        new GetCollection(),        
        new Post(
            denormalizationContext: ['groups' => ['write:Type']],
            security: 'is_granted("ROLE_ADMIN")',
            openapiContext: [
                'security' => [['JWT' => []]]
            ]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['write:Type']],
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
),
ApiFilter(SearchFilter::class, properties: ['name' => 'partial']),
ApiFilter(OrderFilter::class, properties: ['id', 'name'], arguments: ['orderParameterName' => 'order'] ),
]
class Type
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:Type:collection','read:Type:item','read:Movie:collection:full','read:Movie:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:Type:collection','read:Type:item','write:Type','read:Movie:collection:full','read:Movie:item'])]
    #[Assert\NotBlank(message: "The name must not be blank.")]
    #[Assert\Length(
        max: 255, 
        maxMessage: "The name must be at maximum {{ limit }} characters long."
    )]
    private ?string $name = null;

    /**
     * @var Collection<int, Movie>
     */
    #[ORM\ManyToMany(targetEntity: Movie::class, mappedBy: 'types', cascade: ["persist"])]
    #[Groups(['read:Type:item'])]
    private Collection $movies;

    public function __construct()
    {
        $this->movies = new ArrayCollection();
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
     * @return Collection<int, Movie>
     */
    public function getMovies(): Collection
    {
        return $this->movies;
    }

    public function addMovie(Movie $movie): static
    {
        if (!$this->movies->contains($movie)) {
            $this->movies->add($movie);
            $movie->addType($this);
        }

        return $this;
    }

    public function removeMovie(Movie $movie): static
    {
        if ($this->movies->removeElement($movie)) {
            $movie->removeType($this);
        }

        return $this;
    }

    #[SerializedName('movieCount')]
    #[Groups(['read:Type:collection'])]
    public function getMovieCount(): int
    {
        return $this->movies->count();
    }



}
