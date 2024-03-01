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
class User implements UserInterface, PasswordAuthenticatedUserInterface, \Serializable
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

    #[ORM\OneToOne(targetEntity: Profile::class, cascade: ['persist', 'remove'])]
    private ?Profile $profile = null;

    #[ORM\Column]
    private ?bool $actif = null;

    #[ORM\ManyToMany(targetEntity: Meeting::class, mappedBy: 'participants')]
    private Collection $meetingParticipation;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    #[ORM\OneToMany(targetEntity: Meeting::class, mappedBy: 'organizer', orphanRemoval: true)]
    private Collection $meetingsOrganization;

    #[ORM\OneToMany(targetEntity: Messages::class, mappedBy: 'sender', orphanRemoval: true)]
    private Collection $sent;

    #[ORM\OneToMany(targetEntity: Messages::class, mappedBy: 'recipient', orphanRemoval: true)]
    private Collection $receive;

    public function __construct()
    {
        $this->meetingParticipation = new ArrayCollection();
        $this->meetingsOrganization = new ArrayCollection();
        $this->sent = new ArrayCollection();
        $this->receive = new ArrayCollection();
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

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, Meeting>
     */
    public function getMeetingsOrganization(): Collection
    {
        return $this->meetingsOrganization;
    }

    public function addMeetingsOrganization(Meeting $meetingsOrganization): static
    {
        if (!$this->meetingsOrganization->contains($meetingsOrganization)) {
            $this->meetingsOrganization->add($meetingsOrganization);
            $meetingsOrganization->setOrganizer($this);
        }

        return $this;
    }

    public function removeMeetingsOrganization(Meeting $meetingsOrganization): static
    {
        if ($this->meetingsOrganization->removeElement($meetingsOrganization)) {
            // set the owning side to null (unless already changed)
            if ($meetingsOrganization->getOrganizer() === $this) {
                $meetingsOrganization->setOrganizer(null);
            }
        }

        return $this;
    }

    public function serialize(): ?string
    {
        return serialize(array(
            $this->getId(),
            $this->getEmail(),
            $this->getPassword(),
            $this->getLogin(),
            $this->getRoles(),
            $this->isActif()
        ));
    }

    public function unserialize(string $data)
    {
        list(
            $this->id,
            $this->email,
            $this->password,
            $this->login,
            $this->roles,
            $this->actif
            ) = unserialize($data, ['allowed_classes' => false]);
    }

    /**
     * @return Collection<int, Messages>
     */
    public function getSent(): Collection
    {
        return $this->sent;
    }

    public function addSent(Messages $sent): static
    {
        if (!$this->sent->contains($sent)) {
            $this->sent->add($sent);
            $sent->setSender($this);
        }

        return $this;
    }

    public function removeSent(Messages $sent): static
    {
        if ($this->sent->removeElement($sent)) {
            // set the owning side to null (unless already changed)
            if ($sent->getSender() === $this) {
                $sent->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Messages>
     */
    public function getReceive(): Collection
    {
        return $this->receive;
    }

    public function addReceive(Messages $receive): static
    {
        if (!$this->receive->contains($receive)) {
            $this->receive->add($receive);
            $receive->setRecipient($this);
        }

        return $this;
    }

    public function removeReceive(Messages $receive): static
    {
        if ($this->receive->removeElement($receive)) {
            // set the owning side to null (unless already changed)
            if ($receive->getRecipient() === $this) {
                $receive->setRecipient(null);
            }
        }

        return $this;
    }
}
