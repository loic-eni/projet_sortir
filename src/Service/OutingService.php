<?php

namespace App\Service;

use App\Entity\Outing;
use App\Entity\State;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class OutingService
{

    public function __construct(private EntityManagerInterface $entityManager, private UserRepository $userRepository){}

    /**
     * @throws \DateMalformedStringException
     * @throws \DateMalformedIntervalStringException
     */
    public function autoUpdateOutingStates(): void
    {
        $allOutings = $this->entityManager->getRepository(Outing::class)->findAll();

        foreach ($allOutings as $outing) {
            $this->autoSetOutingState($outing, false);
            $this->entityManager->persist($outing);
        }

        $this->entityManager->flush();
    }

    /**
     * @throws \DateMalformedStringException
     * @throws \DateMalformedIntervalStringException
     */
    public function autoSetOutingState(Outing $outing, bool $persist = true): null{
        if($outing->getDuration() < 0) return null;

        $now = new \DateTime();
        $oneMonthAgo = (clone $now)->modify('-1 month');

        $startDate = $outing->getStartDate();
        $endDate = $outing->getStartDate()->add(new \DateInterval('PT' . $outing->getDuration() . 'M'));

        if($startDate <= $oneMonthAgo)
            return $this->setOutingState($outing, State::STATE_ARCHIVED, $persist);
        if($startDate <= $now && $endDate > $now)
            return $this->setOutingState($outing, State::STATE_ACTIVITY_IN_PROGRESS, $persist);
        if($startDate >= $now && ($outing->getMaxInscriptions() <= count($outing->getParticipants()->toArray()) || $outing->getRegistrationMaxDate() <= $now))
            return $this->setOutingState($outing, State::STATE_CLOSED, $persist);
        if($endDate <= $now)
            return $this->setOutingState($outing, State::STATE_PASSED, $persist);

        return null;
    }

    public function setOutingState(Outing $outing, string $stateLabel, bool $persist = true): null{
        $outing->setState($this->entityManager->getRepository(State::class)->findOneBy(['label' => $stateLabel]));
        if($persist){
            $this->entityManager->persist($outing);
            $this->entityManager->flush();
        }
        return null;
    }

    /**
     * Filters outings depending on whether the user has access to it or not, see UserService->hasAccessTo to see the filter rule
     * @param User|UserInterface|null $user
     * @param array $outings
     * @return array
     */
    public function filterOutingsByAccess(User|UserInterface|null $user, array $outings): array{
        $filteredOutings = [];
        foreach ($outings as $outing)
            if($this->hasAccessTo($user, $outing))
                $filteredOutings[] = $outing;

        return $filteredOutings;
    }

    /**
     * A user has access to an outing if:
     *  - the outing is not private
     *  - the outing is private but the user is its organizer
     *  - the outing is private but the user is whitelisted (outing->privateGroup->whiteListedUsers)
     * @param User|UserInterface|null $user
     * @param Outing $outing
     * @return bool
     */
    public function hasAccessTo(User|UserInterface|null $user, Outing $outing): bool{
        if(!$outing->isPrivate())return true;
        if($user === null) return false;

        $user = $this->userRepository->find($user->getId());

        if(gettype($outing) === "string")
            $outing = $this->userRepository->find($outing);

        $privateGroup = $outing->getPrivateGroup();

        return ($privateGroup !== null && $privateGroup->getWhiteListedUsers()->contains($user)) || $outing->getOrganizer() === $user;
    }

}