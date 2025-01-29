<?php

namespace App\Controller;

use App\Controller\Exceptions\DeactivatedAccountException;
use App\Entity\Location;
use App\Form\LocationType;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/location', name: 'location_')]
#[IsGranted('ROLE_USER')]
final class LocationController extends AbstractController
{
    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $entityManager, UserService $userService): Response
    {
        if(!$userService->isUserActive($this->getUser()->getId()))
            throw new DeactivatedAccountException();

        $location = new Location();

        $locationForm = $this->createForm(LocationType::class, $location);
        $locationForm->handleRequest($request);

        if ($locationForm->isSubmitted() && $locationForm->isValid()) {
            $entityManager->persist($location);
            $entityManager->flush();

            $this->addFlash('success', 'Nouvelle localisation enregistrée.');

            return $this->redirectToRoute('outing_create');
        }

        return $this->render('location/create.html.twig', [
            'locationForm' => $locationForm
        ]);
    }
}
