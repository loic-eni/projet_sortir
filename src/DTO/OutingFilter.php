<?php

namespace App\DTO;

use App\Entity\Campus;
use App\Entity\Outing;
use App\Entity\User;

class OutingFilter
{
    private ?Campus $campus = null;
    private ?string $nameSearch = null;
    private ?\DateTime $startsAfter = null;
    private ?\DateTime $startsBefore = null;
    private ?User $user = null;
    private ?bool $userOrganizer = null;
    private ?bool $userRegistered = null;
    private ?bool $outingPast = null;

    public function __construct(){}

    /**
     * @return Campus|null
     */
    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    /**
     * @param Campus|null $campus
     */
    public function setCampus(?Campus $campus): void
    {
        $this->campus = $campus;
    }

    public function getNameSearch(): ?string
    {
        return $this->nameSearch;
    }

    public function setNameSearch(?string $nameSearch): void
    {
        $this->nameSearch = $nameSearch;
    }

    public function getStartsAfter(): ?\DateTime
    {
        return $this->startsAfter;
    }

    public function setStartsAfter(?\DateTime $startsAfter): void
    {
        $this->startsAfter = $startsAfter;
    }

    public function getStartsBefore(): ?\DateTime
    {
        return $this->startsBefore;
    }

    public function setStartsBefore(?\DateTime $startsBefore): void
    {
        $this->startsBefore = $startsBefore;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function isUserOrganizer(): bool | null
    {
        return $this->userOrganizer;
    }

    public function setUserOrganizer(bool $userOrganizer): void
    {
        $this->userOrganizer = $userOrganizer;
    }

    public function isUserRegistered(): bool | null
    {
        return $this->userRegistered;
    }

    public function setUserRegistered(bool $userRegistered): void
    {
        $this->userRegistered = $userRegistered;
    }

    public function isOutingPast(): bool | null
    {
        return $this->outingPast;
    }

    public function setOutingPast(bool $outingPast): void
    {
        $this->outingPast = $outingPast;
    }

    public function isFilterEmpty(){
        return $this->user ===  null && $this->campus === null && $this->userOrganizer === null && $this->outingPast === null && $this->startsBefore === null && $this->startsAfter === null && $this->nameSearch === null;
    }

}