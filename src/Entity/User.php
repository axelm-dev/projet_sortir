<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $login = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Profile $profile = null;

    #[ORM\Column]
    private ?bool $actif = null;

    #[ORM\OneToOne(mappedBy: 'organizer', cascade: ['persist', 'remove'])]
    private ?Meeting $meetingOrganization = null;

    #[ORM\ManyToMany(targetEntity: Meeting::class, mappedBy: 'participants')]
    private Collection $meetingParticipation;

    public function __construct()
    {
        $this->meetingParticipation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface-
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): static
    {
        $this->profile = $profile;
        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    public function getMeetingOrganization(): ?Meeting
    {
        return $this->meetingOrganization;
    }

    public function setMeetingOrganization(Meeting $meetingOrganization): static
    {
        // set the owning side of the relation if necessary
        if ($meetingOrganization->getOrganizer() !== $this) {
            $meetingOrganization->setOrganizer($this);
        }

        $this->meetingOrganization = $meetingOrganization;

        return $this;
    }

    /**
     * @return Collection<int, Meeting>
     */
    public function getMeetingParticipation(): Collection
    {
        return $this->meetingParticipation;
    }

    public function addMeetingParticipation(Meeting $meetingParticipation): static
    {
        if (!$this->meetingParticipation->contains($meetingParticipation)) {
            $this->meetingParticipation->add($meetingParticipation);
            $meetingParticipation->addParticipant($this);
        }

        return $this;
    }

    public function removeMeetingParticipation(Meeting $meetingParticipation): static
    {
        if ($this->meetingParticipation->removeElement($meetingParticipation)) {
            $meetingParticipation->removeParticipant($this);
        }

        return $this;
    }
}
