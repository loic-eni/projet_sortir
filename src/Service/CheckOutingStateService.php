<?php

namespace App\Service;

use App\Entity\Outing;
use App\Entity\State;
use Doctrine\ORM\EntityManagerInterface;

class CheckOutingStateService
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws \DateMalformedStringException
     * @throws \DateMalformedIntervalStringException
     */
    public function checkOutingState(): void
    {
        $allOutings = $this->entityManager->getRepository(Outing::class)->findAll();

        foreach ($allOutings as $outing) {
            $endDateTime = $outing->getStartDate();
            $duration = $outing->getDuration();
            $interval = new \DateInterval('PT' . $duration . 'H');
            $endDateTime->add($interval);
            $isExpired = $endDateTime < (new \DateTime())->modify('-1 month');

            if ($isExpired) {
                $outing->setState($this->entityManager->getRepository(State::class)->findOneBy(['label' => State::STATE_ARCHIVED]));
                $this->entityManager->persist($outing);

            }
        }

        $this->entityManager->flush();
    }

    public function updateState(): void
    {
        $today = new \DateTime();
        $allOutings = $this->entityManager->getRepository(Outing::class)->findAll();

        foreach ($allOutings as $outing) {
            $startDate = $outing->getStartDate();
            $endDate = clone $startDate;
            $endDate->modify('+' . $outing->getDuration() . ' minutes');

            $isExpired = $endDate < (new \DateTime())->modify('-1 month');

            if ($isExpired) {
                $outing->setState($this->entityManager->getRepository(State::class)->findOneBy(['label' => State::STATE_ARCHIVED]));
            }
            elseif ($startDate->format('Y-m-d H:i') === $today->format('Y-m-d H:i')) {
                $outing->setState($this->entityManager->getRepository(State::class)->findOneBy(['label' => State::STATE_ACTIVITY_IN_PROGRESS]));
            }
            elseif ($outing->getMaxInscriptions() === count($outing->getParticipants()->toArray()) && $outing->getState()->getLabel() === State::STATE_OPENED) {
                $outing->setState($this->entityManager->getRepository(State::class)->findOneBy(['label' => State::STATE_CLOSED]));
            }
            elseif ($endDate < $today) {
                $outing->setState($this->entityManager->getRepository(State::class)->findOneBy(['label' => State::STATE_PASSED]));
            }

            $this->entityManager->persist($outing);
        }

        $this->entityManager->flush();
    }


}