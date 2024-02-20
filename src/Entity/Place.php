<?php

namespace App\Entity;

use App\Repository\PlaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlaceRepository::class)]
class Place
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $street = null;

    #[ORM\Column]
    private ?float $latitude = null;

    #[ORM\Column]
    private ?float $longitude = null;

    #[ORM\ManyToOne(inversedBy: 'places')]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $city = null;

    #[ORM\OneToMany(targetEntity: Meeting::class, mappedBy: 'place', orphanRemoval: true)]
    private Collection $meetingsPlace;

    public function __construct()
    {
        $this->meetingsPlace = new ArrayCollection();
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

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): static
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection<int, Meeting>
     */
    public function getMeetingsPlace(): Collection
    {
        return $this->meetingsPlace;
    }

    public function addMeetingsPlace(Meeting $meetingsPlace): static
    {
        if (!$this->meetingsPlace->contains($meetingsPlace)) {
            $this->meetingsPlace->add($meetingsPlace);
            $meetingsPlace->setPlace($this);
        }

        return $this;
    }

    public function removeMeetingsPlace(Meeting $meetingsPlace): static
    {
        if ($this->meetingsPlace->removeElement($meetingsPlace)) {
            // set the owning side to null (unless already changed)
            if ($meetingsPlace->getPlace() === $this) {
                $meetingsPlace->setPlace(null);
            }
        }

        return $this;
    }

}
