<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Entity\MovieHasPeople;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PeopleRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PeopleRepository::class)]
#[ApiResource(
    // TODO : Créer DataProvider pour requetes n+1 si temps
    // TODO : créer doc si temps
    paginationItemsPerPage: 20,
    paginationMaximumItemsPerPage: 50, // TODO : à voir si légitime ou pas 
    paginationClientItemsPerPage: true, // TODO : à voir si légitime ou pas 
    normalizationContext: ['groups' => ['read:People:collection']],
    operations: [
        new Get(normalizationContext: ['groups' => ['read:People:item']]),
        new GetCollection(),
        new Post(
            denormalizationContext: ['groups' => ['write:People']],
            security: 'is_granted("ROLE_ADMIN")',
            openapiContext: [
                'security' => [['JWT' => []]]
            ]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['write:People']],
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
ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'firstname' => 'partial', 'lastname' => 'partial', 'nationality' => 'exact']),
ApiFilter(OrderFilter::class, properties: ['id', 'firstname', 'lastname', 'dateOfBirth'], arguments: ['orderParameterName' => 'order'] ),
]
class People
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read:People:item','read:People:collection','read:Movie:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:People:item','read:People:collection','read:Movie:item','write:People'])]
    #[Assert\NotBlank(message: "The first name must not be blank.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "The first name must be at maximum {{ limit }} characters long."
    )]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:People:item','read:People:collection','read:Movie:item','write:People'])]
    #[Assert\NotBlank(message: "The last name must not be blank.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "The last name must be at maximum {{ limit }} characters long."
    )]
    private ?string $lastname = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['read:People:item','read:People:collection','read:Movie:item','write:People'])]
    #[Assert\NotBlank(message: "The date of birth must not be blank.")]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read:People:item','read:People:collection','read:Movie:item','write:People'])]
    #[Assert\NotBlank(message: "The nationality must not be blank.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "The nationality must be at maximum {{ limit }} characters long."
    )]
    private ?string $nationality = null;

    /**
     * @var Collection<int, MovieHasPeople>
     */
    #[ORM\OneToMany(mappedBy: 'people', targetEntity: MovieHasPeople::class, cascade: ["remove"])]
    private Collection $movieRoles;

    public function __construct()
    {
        $this->movieRoles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTimeInterface $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(string $nationality): static
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * @return Collection<int, MovieHasPeople>
     */
    public function getMovieRoles(): Collection
    {
        return $this->movieRoles;
    }

    public function addMovieRole(MovieHasPeople $movieRole): static
    {
        if (!$this->movieRoles->contains($movieRole)) {
            $this->movieRoles->add($movieRole);
            $movieRole->setPeople($this);
        }
        return $this;
    }

    public function removeMovieRole(MovieHasPeople $movieRole): static
    {
        if ($this->movieRoles->removeElement($movieRole)) {
            if ($movieRole->getPeople() === $this) {
                $movieRole->setPeople(null);
            }
        }
        return $this;
    }

    #[SerializedName('movieCount')]
    #[Groups(['read:People:collection'])] 
    public function getMovieCount(): int
    {
        return $this->movieRoles->count();
    }

}

