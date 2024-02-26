<?php

namespace App\Entity;

use App\Repository\MeetingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeetingRepository::class)]
class Meeting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?int $usersMax = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $textNote = null;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $limitDate = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'meetingParticipation')]
    private Collection $participants;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?StateMeeting $state = null;

    #[ORM\ManyToOne(inversedBy: 'meetingsPlace')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Place $place = null;

    #[ORM\ManyToOne(inversedBy: 'meetings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $annulation_reason = null;

    #[ORM\ManyToOne(inversedBy: 'meetingsOrganization')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $organizer = null;

    private ?int $nb_user = 0;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getUsersMax(): ?int
    {
        return $this->usersMax;
    }

    public function setUsersMax(int $usersMax): static
    {
        $this->usersMax = $usersMax;

        return $this;
    }

    public function getTextNote(): ?string
    {
        return $this->textNote;
    }

    public function setTextNote(?string $textNote): static
    {
        $this->textNote = $textNote;

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

    public function getLimitDate(): ?\DateTimeInterface
    {
        return $this->limitDate;
    }

    public function setLimitDate(\DateTimeInterface $limitDate): static
    {
        $this->limitDate = $limitDate;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }

        return $this;
    }

    public function removeParticipant(User $participant): static
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    public function getState(): ?StateMeeting
    {
        return $this->state;
    }

    public function setState(?StateMeeting $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): static
    {
        $this->place = $place;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    public function getAnnulationReason(): ?string
    {
        return $this->annulation_reason;
    }

    public function setAnnulationReason(?string $annulation_reason): static
    {
        $this->annulation_reason = $annulation_reason;

        return $this;
    }

    public function getOrganizer(): ?User
    {
        return $this->organizer;
    }

    public function setOrganizer(?User $organizer): static
    {
        $this->organizer = $organizer;

        return $this;
    }

    public function getNbUser(): ?int
    {
        return $this->nb_user;
    }

    public function setNbUser(?int $nb_user): static
    {
        $this->nb_user = $nb_user;

        return $this;
    }
}
