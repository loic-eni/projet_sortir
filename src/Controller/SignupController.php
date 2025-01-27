<?php

namespace App\Controller;

use App\Form\SignupCsvType;
use App\Form\SignupType;
use App\Service\SignupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class SignupController extends AbstractController
{
    #[Route('/signup', name: 'app_signup')]
    #[IsGranted("ROLE_ADMIN")]
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

    #[Route('/signupWithCsv', name: 'app_signup_with_csv')]
    #[IsGranted('ROLE_ADMIN')]
    public function registerUsersWithCsv(Request $request, SignupService $signupService): Response{
        $form = $this->createForm(SignupCsvType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $csv */
            $csv = $form->get('csv')->getData();

            if($csv){
                $content = $csv->getContent();
                // remove headers
                $content = substr($content, 3);

                $results = $signupService->signUpWithCSV($content);

                if($results === false){
                    $this->addFlash('error', 'La création de comptes a echouée');
                }else{
                    $createdAccounts = $results['createdAccounts'];
                    $invalidLines = $results['invalidLines'];

                    if(count($createdAccounts) > 0)
                        $this->addFlash('success', 'Comptes créés pour: ' . implode(', ', array_map(function($account){return $account->getEmail();}, $createdAccounts)));
                    if(count($invalidLines) > 0)
                        $this->addFlash('error', 'Création de compte impossible pour les lignes: ' . implode(', ', $invalidLines));
                }

            }

            return new Response($this->renderView('signup/csv.html.twig', ['form' => $form]), 201);
        }

        return $this->render('signup/csv.html.twig', ['form' => $form]);
    }
}
