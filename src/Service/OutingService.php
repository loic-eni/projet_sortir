<?php

namespace App\Service;

use App\Entity\Outing;
use App\Entity\State;
use Doctrine\ORM\EntityManagerInterface;

readonly class OutingService
{
    public function __construct(private EntityManagerInterface $entityManager){}

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

    public function isVisibleOuting(Outing $outing): bool
    {
        return !$outing->getLocation()->isDeleted();
    }
}