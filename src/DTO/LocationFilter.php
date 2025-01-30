<?php

namespace App\DTO;

class LocationFilter
{
    private ?string $name = null;

    public function __construct(){}

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function isFilterEmpty(){
        return $this->name ===  null;
    }
}