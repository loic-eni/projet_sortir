<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Form\OutingType;
use App\Repository\OutingRepository;
use App\Repository\StateRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/outing', name: 'outing_')]
final class OutingController extends BaseController
{
    #[Route('/list', name: 'list', methods: ['GET'])]
    public function list(): Response
    {
        return $this->render('outing/list.html.twig', [
            'controller_name' => 'OutingController',
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

    #[Route('/details/{id}', name: 'details', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function details(int $id,
                            Request $request,
                           EntityManagerInterface $entityManager,
                           OutingRepository $outingRepository
    ): Response
    {
        $outing = $outingRepository->find($id);

        $participants = $outing->getParticipants();

        if(!$outing){
            throw $this->createNotFoundException("Outing not found");
        }

        return $this->render('outing/details.html.twig', [
                'outing' => $outing,
                'participants' => $participants,
                'state' => self::STATE
            ]
        );
    }

    #[Route('/register/new/{id}', name: 'register_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager, Outing $outing): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

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

        $user = $this->getUser();

        if ($outing->getParticipants()->contains($user)) {
            $outing->removeParticipant($user);

            $entityManager->persist($outing);
            $entityManager->flush();

            $this->addFlash('success', 'Vous avez été retiré de la sortie.');
        } else {
            $this->addFlash('warning', 'Vous n\'êtes pas inscrit à cette sortie.');
        }

        return $this->redirectToRoute('outing_details', ['id' => $outing->getId()]);
    }

}
