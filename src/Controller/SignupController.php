<?php

namespace App\Controller;

use App\Form\SignupType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SignupController extends AbstractController
{
    #[Route('/signup', name: 'app_signup')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(SignupType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));
            $user->setRoles(['ROLE_USER']);
            $user->setAdmin(false);
            $user->setActive(false);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Compte créé.');

            return $this->redirectToRoute('app_main');
        }

        return $this->render('signup/index.html.twig', [
            'controller_name' => 'SignupController',
            'signupForm' => $form,
        ]);
    }
}
