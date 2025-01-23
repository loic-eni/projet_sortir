<?php

namespace App\Controller;

use App\DTO\OutingFilter;
use App\Entity\Outing;
use App\Form\OutingFilterType;
use App\Form\OutingType;
use App\Repository\CampusRepository;
use App\Repository\OutingRepository;
use App\Repository\UserRepository;
use App\Utils\DateTimeUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/outing', name: 'outing_')]
final class OutingController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function list(CampusRepository $campusRepository, OutingRepository $outingRepository,UserRepository $userRepository, Request $request): Response
    {

        $user = $this->getUser();
        if($user)
            $user = $userRepository->find($user->getId());

        $outingFilter = new OutingFilter();
        $form = $this->createForm(OutingFilterType::class, $outingFilter);
        $form->handleRequest($request);

        dump($form->getErrors());
        dump($request->getMethod());
        dump($user != null);

        if($form->isSubmitted() && $form->isValid() && $user != null){
            $outingFilter = $form->getData();
            $outingFilter->setUser($user);

            dump($outingFilter);

            $outings = $outingRepository->findByFilter($outingFilter);

            return $this->render('outing/list.html.twig', [
                'outings' => $outings,
                'outingFilterForm' => $form,
            ]);
        }

        $outings = $outingRepository->findAll();

        return $this->render('outing/list.html.twig', [
            'outings' => $outings,
            'outingFilterForm' => $form,
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

        if(!$outing){
            throw $this->createNotFoundException("Outing not found");
        }

        return $this->render('outing/details.html.twig', [
                'outing' => $outing,
            ]
        );
    }
}
