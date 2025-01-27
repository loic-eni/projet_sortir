<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\EditAccountType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(EntityManagerInterface $entityManager, Request $request, SluggerInterface $slugger,
                          #[Autowire('%kernel.project_dir%/public/uploads/profile_image')] string $imgDirectory): Response
    {
        $form = $this->createForm(EditAccountType::class);
        $form->setData($this->getUser());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            /** @var UploadedFile $brochureFile */
            $imgPath = $form->get('imgPath')->getData();
            if ($imgPath) {
                $originalFilename = pathinfo($imgPath->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imgPath->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imgPath->move($imgDirectory, $newFilename);
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $user->setImgPath($newFilename);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Compte modifié.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/index.html.twig', [
            'editAccountForm' => $form,
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

            $user = $this->getUser();
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Mot de passe changé.');

            return $this->redirectToRoute('app_main');
        }

        return $this->render('user/changePassword.html.twig', ['changePasswordForm' => $form]);
    }

    #[Route('/profile/user/{id}', name: 'app_show_profile', methods: ['GET'])]
    public function showProfile(User $user): Response
    {
        return $this->render('outing/show_profile.html.twig', [
            'user' => $user,
        ]);
    }
}
