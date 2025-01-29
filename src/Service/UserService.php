<?php

namespace App\Service;

use App\Entity\Outing;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserService
{
    public function __construct(private UserRepository $userRepository, private EntityManagerInterface $entityManager){}

    public function isUserActive(string $id){
        return $this->userRepository->find($id)->isActive();
    }

    public function hasAccessTo(User|UserInterface|null $user, Outing|string $outing){
        if(!$outing->isPrivate())return true;
        if($user === null) return false;

        $user = $this->userRepository->find($user->getId());

        if(gettype($outing) === "string")
            $outing = $this->userRepository->find($outing);

        return $outing->getWhiteListedUsers()->contains($user) || $outing->getOrganizer() === $user;
    }
}