<?php

namespace App\Controller;

use App\Entity\Location;
use App\Form\LocationFilterType;
use App\Form\LocationType;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function PHPUnit\Framework\throwException;

#[Route('/location', name: 'location_')]
final class LocationController extends AbstractController
{
    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $location = new Location();

        $locationForm = $this->createForm(LocationType::class, $location);
        $locationForm->handleRequest($request);

        if ($locationForm->isSubmitted() && $locationForm->isValid()) {
            $entityManager->persist($location);
            $entityManager->flush();

            $this->addFlash('success', 'Nouvelle localisation enregistrée.');

            $referer = $request->query->get('redirect');

            if($referer){
                return $this->redirectToRoute('location_list');
            }

            return $this->redirectToRoute('outing_create');
        }

        return $this->render('location/create.html.twig', [
            'locationForm' => $locationForm
        ]);
    }

    #[Route('/list', name: 'list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function list(LocationRepository $locationRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LocationFilterType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $locationFilter = $form->getData();

            $locations = $locationRepository->findByFilter($locationFilter);

            return $this->render('location/list.html.twig', [
                'locations' => $locations,
                'filterEmpty' => $locationFilter->isFilterEmpty(),
                'form' => $form
            ]);
        }

        $locations = $entityManager->getRepository(Location::class)->findAllNotDeleted();

        return $this->render('location/list.html.twig', [
            'locations' => $locations,
            'form' => $form
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $location = $entityManager->getRepository(Location::class)->find($id);

        if($location->isDeleted()) {
            throw $this->createNotFoundException('La localisation n\'existe pas.');
        }

        $location->softDelete();
        $entityManager->flush();

        $this->addFlash('success', 'La localisation ' . $location->getName() . ' à bien été supprimé');

        return $this->redirectToRoute('location_list');
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Location $location, EntityManagerInterface $entityManager): Response
    {
        if($location->isDeleted()) {
            throw $this->createNotFoundException('La localisation n\'existe pas.');
        }

        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'La localisation ' . $location->getName() . ' à bien été modifiée');

            return $this->redirectToRoute('location_list');
        }

        return $this->render('location/edit.html.twig', [
            'location' => $location,
            'locationForm' => $form
        ]);
    }
}
