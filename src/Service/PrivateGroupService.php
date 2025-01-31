<?php

namespace App\Service;

use App\Entity\PrivateGroup;
use App\Entity\User;
use App\Repository\PrivateGroupRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class PrivateGroupService
{

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly  PrivateGroupRepository $privateGroupRepository,
    ){}
    public function hasAccessTo(User|UserInterface $user, PrivateGroup $group){
        $user = $this->userRepository->find($user->getId());
        return $this->isMember($user, $group) || $this->isOwner($user, $group);
    }

    public function isOwner(User|UserInterface $user, PrivateGroup $group){
        $user = $this->userRepository->find($user->getId());
        return $group->getOwner() === $user;
    }

    public function isMember(User|UserInterface $user, PrivateGroup $group){
        $user = $this->userRepository->find($user->getId());
        return $group->getWhiteListedUsers()->contains($user);
    }

    public function getOwnedGroups(User|UserInterface $user){
        return $this->privateGroupRepository->findBy(['owner' => $user]);
    }
}