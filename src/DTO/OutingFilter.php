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
    private ?\DateTime $endsBefore = null;
    private ?User $user = null;
    private bool $whereUserIsOrganizer = false;
    private bool $whereUserIsRegistered = false;
    private bool $outingIsPast = false;

    public function __construct(){}

    public function whereCampus(?Campus $campus){
        $this->campus = $campus;
        return $this;
    }
    public function whereNameContains(?string $nameSearch){
        $this->nameSearch = $nameSearch;
        return $this;
    }
    public function withUser(User $user){
        $this->user = $user;
        return $this;
    }
    public function whereUserIsOrganizer(bool $userIsOrganizer){
        $this->whereUserIsOrganizer = $userIsOrganizer | false;
        return $this;
    }
    public function whereUserIsRegistered(bool $userIsRegistered){
        $this->whereUserIsRegistered = $userIsRegistered | false;
        return $this;
    }
    public function whereOutingIsPast(bool $outingIsPast){
        $this->outingIsPast = $outingIsPast | false;
        return $this;
    }
    public function whereStartsAfter(?\DateTime $startsAfter){
        $this->startsAfter = $startsAfter;
        return $this;
    }
    public function whereEndsBefore(?\DateTime $endsBefore){
        $this->endsBefore = $endsBefore;
        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }
    public function getNameSearch(): ?string
    {
        return $this->nameSearch;
    }
    public function getStartsAfter(): ?\DateTime
    {
        return $this->startsAfter;
    }
    public function getUser(): ?User
    {
        return $this->user;
    }
    public function getEndsBefore(): ?\DateTime
    {
        return $this->endsBefore;
    }
    public function isUserOrganizer(): bool
    {
        return $this->whereUserIsOrganizer;
    }
    public function isUserRegistered(): bool
    {
        return $this->whereUserIsRegistered;
    }
    public function isOutingPast(): bool
    {
        return $this->outingIsPast;
    }


}