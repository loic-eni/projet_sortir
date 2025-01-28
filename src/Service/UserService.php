<?php

namespace App\Service;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    public function __construct(private UserRepository $userRepository, private EntityManagerInterface $entityManager){}

    public function isUserActive(string $id){
        return $this->userRepository->find($id)->isActive();
    }
}