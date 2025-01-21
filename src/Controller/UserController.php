<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Form\EditAccountType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(EditAccountType::class);
        $form->setData($this->getUser());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Account updated.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'editAccountForm' => $form->createView(),
        ]);
    }

    #[Route('/changePassword', name: 'app_changePassword')]
    public function changePassword(EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $password = $data['password'];
            $passwordConfirmation = $data['password_confirmation'];

            if($password !== $passwordConfirmation)
                return $this->render('user/changePassword.html.twig', ['changePasswordForm' => $form->createView(), 'errors'=>['Password does not match password confirmation']]);

            $user = $this->getUser();
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Password changed.');

            return $this->redirectToRoute('app_main');
        }

        return $this->render('user/changePassword.html.twig', ['changePasswordForm' => $form->createView()]);
    }
}
