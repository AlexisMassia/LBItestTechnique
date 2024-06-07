<?php

namespace App\Entity;

use App\Entity\MovieHasPeople;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PeopleRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: PeopleRepository::class)]
class People
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[ORM\Column(length: 255)]
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

}

