<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Entity\State;
use App\Entity\User;
use App\Form\OutingCancel;
use App\Form\OutingFilterType;
use App\Form\OutingType;
use App\Repository\CampusRepository;
use App\Repository\OutingRepository;
use App\Repository\StateRepository;
use App\Repository\UserRepository;
use App\Service\OutingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/outing', name: 'outing_')]
final class OutingController extends BaseController
{
    #[Route('/list', name: 'list')]
    public function list(OutingRepository $outingRepository, UserRepository $userRepository, Request $request, OutingService $outingService): Response
    {
        $user = $this->getUser();
        if($user)
            $user = $userRepository->find($user->getId());

        $form = $this->createForm(OutingFilterType::class);
        $form->handleRequest($request);

        $outingService->autoUpdateOutingStates();

        if($form->isSubmitted() && $form->isValid() && $user != null){
            $outingFilter = $form->getData();
            $outingFilter->setUser($user);

            dump($outingFilter);

            $outings = $outingRepository->findByFilter($outingFilter);

            dump($outings);

            return $this->render('outing/list.html.twig', [
                'outings' => $outings,
                'form' => $form,
                'outingStates'=>$this::STATE
            ]);
        }

        $outings = $outingRepository->findAll();

        return $this->render('outing/list.html.twig', [
            'outings'=>$outings,
            'form' => $form,
            'outingStates'=>$this::STATE
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_USER")]
    public function create(Request $request,
                           EntityManagerInterface $entityManager,
                           OutingRepository $outingRepository
    ): Response
    {
        $outing = new Outing();

        $outingForm = $this->createForm(OutingType::class, $outing);
        $outingForm->handleRequest($request);

        if($outingForm->isSubmitted() && $outingForm->isValid()){
            $outing->setOrganizer($this->getUser());
            $outing->setState($entityManager->getRepository(State::class)->findOneBy(['label' => State::STATE_CREATED]));

            $entityManager->persist($outing);
            $entityManager->flush();

            $this->addFlash('success', 'Nouvelle sortie enregistrée.');

            return $this->redirectToRoute('outing_list');
        }

        return $this->render('outing/create.html.twig', [
            'outingForm' => $outingForm,
            ]
        );
    }

    #[Route('/{id}/publish', name: 'publish', methods: ['GET', 'POST'])]
    public function publish(Outing $outing, StateRepository $stateRepository,  EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();

        if ($currentUser !== $outing->getOrganizer()) {
            throw new AccessDeniedException("Vous n'êtes pas autorisé à accéder à cette page.");
        }

        $outing->setState($stateRepository->findOneBy(['label' => State::STATE_OPENED ]));
        $entityManager->flush();

        return $this->redirectToRoute('outing_details', ['id' => $outing->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/details/{id}', name: 'details', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function details(
        int $id,
        OutingRepository $outingRepository
    ): Response {
        $outing = $outingRepository->find($id);

        $currentDate = new \DateTime();
        $oneMonthAgo = $currentDate->modify('-1 month');

        if ($outing && $outing->getStartDate() < $oneMonthAgo) {
            $this->addFlash('error', 'Cette sortie ne peut plus être consultée car elle a eu lieu il y a plus d\'un mois.');

            return $this->redirectToRoute('outing_list');
        }

        $participants = $outing->getParticipants();

        return $this->render('outing/details.html.twig', [
            'outing' => $outing,
            'participants' => $participants,
            'state' => self::STATE
        ]);
    }


    #[Route('/register/new/{id}', name: 'register_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Outing $outing): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        /** @var User $user */
        $user = $this->getUser();

        if ($outing->getParticipants()->contains($user)) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à cette sortie.');
            return $this->redirectToRoute('outing_details', ['id' => $outing->getId()]);
        }

        $status = $outing->getState()->getLabel();
        if (!in_array($status, [self::STATE[0], self::STATE[1]]) ||
            count($outing->getParticipants()) >= $outing->getMaxInscriptions() ||
            $outing->getRegistrationMaxDate() < new \DateTime()) {
            $this->addFlash('warning', 'Impossible de vous inscrire à cette sortie (soit le statut n\'est pas "Ouverte", soit le nombre d\'inscriptions est atteint ou soit date limite d\'inscription est dépassé)');
            return $this->redirectToRoute('outing_details', ['id' => $outing->getId()]);
        }

        $outing->addParticipant($user);
        $entityManager->persist($outing);
        $entityManager->flush();
        $this->addFlash('success', 'Vous avez été inscrit à la sortie.');


        return $this->redirectToRoute('outing_details', ['id' => $outing->getId()], Response::HTTP_SEE_OTHER);
    }



    #[Route('/register/remove/{id}', name: 'register_remove', methods: ['GET', 'POST'])]
    public function remove(EntityManagerInterface $entityManager, Outing $outing): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        /** @var User $user */
        $user = $this->getUser();

        if ($outing->getParticipants()->contains($user)) {

            if ($outing->getStartDate() > new \DateTime()) {
                $outing->removeParticipant($user);
                $entityManager->persist($outing);
                $entityManager->flush();

                $this->addFlash('success', 'Vous avez été retiré de la sortie.');
            } else {
                $this->addFlash('warning', 'Vous ne pouvez pas vous désister, la sortie a déjà commencé.');
            }
        } else {
            $this->addFlash('warning', 'Vous n\'êtes pas inscrit à cette sortie.');
        }

        return $this->redirectToRoute('outing_details', ['id' => $outing->getId()]);
    }

    #[Route('/{id}/cancel', name: 'cancel', methods: ['GET', 'POST'])]
    public function cancel(Request $request, Outing $outing, EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();

        if ($currentUser !== $outing->getOrganizer()) {
            throw new AccessDeniedException("Vous n'êtes pas autorisé à accéder à cette page.");
        }

        if ($outing->getStartDate() <= new \DateTime()) {
            throw new AccessDeniedException("La sortie a déjà commencé ou est passée, vous ne pouvez pas l'annuler.");
        }

        $form = $this->createForm(OutingCancel::class, $outing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $outing->setState($entityManager->getRepository(State::class)->findOneBy(['label' => State::STATE_CANCELED]));
            $entityManager->flush();

            $this->addFlash('success', 'La sortie ' . $outing->getName() . ' a bien été annulée.');
            return $this->redirectToRoute('outing_details', ['id' => $outing->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('outing/cancel.html.twig', [
            'outing' => $outing,
            'form_cancel' => $form,
        ]);
    }
}
