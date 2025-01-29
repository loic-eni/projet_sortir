<?php

namespace App\Entity;

use App\Repository\OutingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: OutingRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'Il existe déja une sortie du même nom')]
class Outing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $registrationMaxDate = null;

    #[ORM\Column(nullable: true)]
    private ?int $maxInscriptions = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $outingInfo = null;

    #[ORM\ManyToOne]
    private ?state $state = null;

    #[ORM\ManyToOne(inversedBy: 'outings')]
    private ?location $location = null;

    #[ORM\ManyToOne(inversedBy: 'outings')]
    private ?campus $campus = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'outing')]
    private Collection $participants;

    #[ORM\ManyToOne(inversedBy: 'outings')]
    private ?user $organizer = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reason = null;

    #[ORM\Column]
    private ?bool $private = null;

    #[ORM\ManyToOne(inversedBy: 'outings')]
    private ?PrivateGroup $privateGroup = null;

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

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

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

    public function getRegistrationMaxDate(): ?\DateTimeInterface
    {
        return $this->registrationMaxDate;
    }

    public function setRegistrationMaxDate(?\DateTimeInterface $registrationMaxDate): static
    {
        $this->registrationMaxDate = $registrationMaxDate;

        return $this;
    }

    public function getMaxInscriptions(): ?int
    {
        return $this->maxInscriptions;
    }

    public function setMaxInscriptions(?int $maxInscriptions): static
    {
        $this->maxInscriptions = $maxInscriptions;

        return $this;
    }

    public function getOutingInfo(): ?string
    {
        return $this->outingInfo;
    }

    public function setOutingInfo(?string $outingInfo): static
    {
        $this->outingInfo = $outingInfo;

        return $this;
    }

    public function getState(): ?state
    {
        return $this->state;
    }

    public function setState(?state $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getLocation(): ?location
    {
        return $this->location;
    }

    public function setLocation(?location $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getCampus(): ?campus
    {
        return $this->campus;
    }

    public function setCampus(?campus $campus): static
    {
        $this->campus = $campus;

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
            $participant->addOuting($this);
        }

        return $this;
    }

    public function removeParticipant(User $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            $participant->removeOuting($this);
        }

        return $this;
    }

    public function getOrganizer(): ?user
    {
        return $this->organizer;
    }

    public function setOrganizer(?user $organizer): static
    {
        $this->organizer = $organizer;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function isPrivate(): ?bool
    {
        return $this->private;
    }

    public function setPrivate(bool $private): static
    {
        $this->private = $private;

        return $this;
    }

    public function getPrivateGroup(): ?PrivateGroup
    {
        return $this->privateGroup;
    }

    public function setPrivateGroup(?PrivateGroup $privateGroup): static
    {
        $this->privateGroup = $privateGroup;

        return $this;
    }
}
