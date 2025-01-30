<?php

namespace App\Controller;

use App\Controller\Exceptions\AccessDeniedException;
use App\Controller\Exceptions\DeactivatedAccountException;
use App\DTO\Redirection;
use App\Entity\Outing;
use App\Entity\State;
use App\Entity\User;
use App\Form\OutingCancel;
use App\Form\OutingFilterType;
use App\Form\OutingType;
use App\Repository\OutingRepository;
use App\Repository\StateRepository;
use App\Repository\UserRepository;
use App\Service\OutingService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/outing', name: 'outing_')]
final class OutingController extends BaseController
{

    public function __construct(
        private readonly OutingRepository       $outingRepository,
        private readonly OutingService          $outingService,
        private readonly UserService            $userService,
        private readonly UserRepository         $userRepository,
        private readonly EntityManagerInterface $entityManager
    ){}

    #[Route('/list', name: 'list')]
    public function list(Request $request): Response
    {
        $user = $this->getUser();
        if($user)
            $user = $this->userRepository->find($user->getId());

        $form = $this->createForm(OutingFilterType::class);
        $form->handleRequest($request);

        $this->outingService->autoUpdateOutingStates();

        if($form->isSubmitted() && $form->isValid() && $user != null){
            $outingFilter = $form->getData();
            $outingFilter->setUser($user);

            $outings = $this->outingRepository->findByFilter($outingFilter);
            $outings = $this->outingService->filterOutingsByAccess($user, $outings);

            return $this->render('outing/list.html.twig', [
                'outings' => $outings,
                'form' => $form,
                'filterEmpty'=>$outingFilter->isFilterEmpty(),
                'outingStates'=>$this::STATE
            ]);
        }

        $outings = $this->outingRepository->findAll();
        $outings = $this->outingService->filterOutingsByAccess($user, $outings);

        return $this->render('outing/list.html.twig', [
            'outings'=>$outings,
            'form' => $form,
            'filterEmpty'=>true,
            'outingStates'=>$this::STATE
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_USER")]
    public function create(Request $request): Response
    {
        if(!$this->userService->isUserActive($this->getUser()->getId()))
            throw new DeactivatedAccountException();

        $outing = new Outing();

        $outingForm = $this->createForm(OutingType::class, $outing);
        $outingForm->handleRequest($request);

        if($outingForm->isSubmitted() && $outingForm->isValid()){
            $outing->setOrganizer($this->getUser());
            $outing->setState($this->entityManager->getRepository(State::class)->findOneBy(['label' => State::STATE_CREATED]));
            if(!$outing->isPrivate())
                $outing->setPrivateGroup(null);

            $this->entityManager->persist($outing);
            $this->entityManager->flush();

            $this->addFlash('success', 'Nouvelle sortie enregistrée.');

            return $this->redirectToRoute('outing_list');
        }

        return $this->render('outing/create.html.twig', [
            'outingForm' => $outingForm,
            ]
        );
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_USER")]
    public function edit(Request $request, Outing $outing): Response
    {
        $currentUser = $this->getUser();
        if ($currentUser !== $outing->getOrganizer())
            throw new AccessDeniedException("Vous n'êtes pas autorisé à accéder à cette page.");


        if(!$this->userService->isUserActive($this->getUser()->getId()))
            throw new DeactivatedAccountException();

        if ($outing->getState()->getLabel() !== State::STATE_CREATED) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier une sortie dont le statut n\'est pas "Créé".');
            return $this->redirectToRoute('outing_list');
        }

        $form = $this->createForm(OutingType::class, $outing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'La sortie ' . $outing->getName() . ' a bien été edité.');

            return $this->redirectToRoute('outing_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('outing/edit.html.twig', [
            'outing' => $outing,
            'outingForm' => $form,
        ]);
    }

    #[Route('/publish/{id}', name: 'publish', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_USER")]
    public function publish(Outing $outing, StateRepository $stateRepository): Response
    {
        $currentUser = $this->getUser();
        if ($currentUser !== $outing->getOrganizer())
            throw new AccessDeniedException("Vous n'êtes pas autorisé à accéder à cette page.");

        if(!$this->userService->isUserActive($this->getUser()->getId()))
            throw new DeactivatedAccountException();

        $outing->setState($stateRepository->findOneBy(['label' => State::STATE_OPENED ]));
        $this->entityManager->flush();

        return $this->redirectToRoute('outing_details', ['id' => $outing->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/details/{id}', name: 'details', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function details(int $id, ?Redirection $redirection): Response {
        $outing = $this->outingRepository->find($id);

        if(!$this->outingService->hasAccessTo($this->getUser(), $outing))
            throw new AccessDeniedException('Cette sortie est privée et vous n\'y avez pas accès.');

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
            'state' => self::STATE,
            'redirection' => $redirection
        ]);
    }


    #[Route('/register/new/{id}', name: 'register_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_USER")]
    public function new(Outing $outing, ?Redirection $redirection): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if(!$this->userService->isUserActive($this->getUser()->getId()))
            throw new DeactivatedAccountException();

        /** @var User $user */
        $user = $this->getUser();

        if ($outing->getParticipants()->contains($user)) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit à cette sortie.');
            return $this->redirectToRoute('outing_details', ['id' => $outing->getId(), 'redirection'=>$redirection->toArray()]);
        }

        $status = $outing->getState()->getLabel();
        if (!in_array($status, [self::STATE[0], self::STATE[1]]) ||
            count($outing->getParticipants()) >= $outing->getMaxInscriptions() ||
            $outing->getRegistrationMaxDate() < new \DateTime()) {
            $this->addFlash('warning', 'Impossible de vous inscrire à cette sortie (soit le statut n\'est pas "Ouverte", soit le nombre d\'inscriptions est atteint ou soit date limite d\'inscription est dépassé)');
            return $this->redirectToRoute('outing_details', ['id' => $outing->getId(), 'redirection'=>$redirection->toArray()]);
        }

        $outing->addParticipant($user);
        $this->entityManager->persist($outing);
        $this->entityManager->flush();
        $this->addFlash('success', 'Vous avez été inscrit à la sortie.');


        return $this->redirectToRoute('outing_details', ['id' => $outing->getId(), 'redirection'=>$redirection->toArray()], Response::HTTP_SEE_OTHER);
    }



    #[Route('/register/remove/{id}', name: 'register_remove', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_USER")]
    public function remove(Outing $outing, ?Redirection $redirection): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if(!$this->userService->isUserActive($this->getUser()->getId()))
            throw new DeactivatedAccountException();

        /** @var User $user */
        $user = $this->getUser();

        if ($outing->getParticipants()->contains($user)) {

            if ($outing->getStartDate() > new \DateTime()) {
                $outing->removeParticipant($user);
                $this->entityManager->persist($outing);
                $this->entityManager->flush();

                $this->addFlash('success', 'Vous avez été retiré de la sortie.');
            } else {
                $this->addFlash('warning', 'Vous ne pouvez pas vous désister, la sortie a déjà commencé.');
            }
        } else {
            $this->addFlash('warning', 'Vous n\'êtes pas inscrit à cette sortie.');
        }

        return $this->redirectToRoute('outing_details', ['id' => $outing->getId(), 'redirection'=>$redirection->toArray()]);
    }

    #[Route('/cancel/{id}', name: 'cancel', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_USER")]
    public function cancel(Request $request, Outing $outing): Response
    {
        $currentUser = $this->getUser();

        if ($currentUser !== $outing->getOrganizer() && !$currentUser->isAdmin()) {
            throw new AccessDeniedException("Vous n'êtes pas autorisé à accéder à cette page.");
        }

        if ($outing->getStartDate() <= new \DateTime()) {
            throw new AccessDeniedException("La sortie a déjà commencé ou est passée, vous ne pouvez pas l'annuler.");
        }

        $form = $this->createForm(OutingCancel::class, $outing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $outing->setState($this->entityManager->getRepository(State::class)->findOneBy(['label' => State::STATE_CANCELED]));
            $this->entityManager->flush();

            $this->addFlash('success', 'La sortie ' . $outing->getName() . ' a bien été annulée.');
            return $this->redirectToRoute('outing_details', ['id' => $outing->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('outing/cancel.html.twig', [
            'outing' => $outing,
            'form_cancel' => $form,
        ]);
    }
}
