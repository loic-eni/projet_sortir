<?php

namespace App\Controller;

use App\Controller\Exceptions\AccessDeniedException;
use App\DTO\Redirection;
use App\Entity\PrivateGroup;
use App\Form\PrivateGroupType;
use App\Service\PrivateGroupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class PrivateGroupController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PrivateGroupService $privateGroupService
    ){}

    #[Route('/private/group', name: 'app_private_group')]
    public function index(): Response
    {
        return $this->render('private_group/index.html.twig', [
            'controller_name' => 'PrivateGroupController',
        ]);
    }

    #[Route('/private_group/create', name: 'app_private_group_create')]
    public function create(Request $request, ?Redirection $redirection): Response{
        $form = $this->createForm(PrivateGroupType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            /** @var PrivateGroup $privateGroup */
            $privateGroup = $form->getData();
            $privateGroup->setOwner($this->getUser());
            $this->entityManager->persist($privateGroup);
            $this->entityManager->flush();
            $this->addFlash('success', 'Nouveau groupe privé "' . $privateGroup->getName() . '" créé');

            return $this->redirectToRoute('app_private_group_details', ['id' => $privateGroup->getId(), 'redirection'=>$redirection->toArray()]);
        }

        return $this->render('private_group/create.html.twig', [
            'redirection'=>$redirection,
            'form'=>$form,
        ]);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/private_group/{id}', name: 'app_private_group_details')]
    public function details(PrivateGroup $privateGroup, ?Redirection $redirection): Response{
        if(!$this->privateGroupService->hasAccessTo($this->getUser(), $privateGroup))
            throw new AccessDeniedException('Vous n\'avez pas accès à ce groupe privé');

        return $this->render('private_group/details.html.twig', ['group'=>$privateGroup, 'redirection'=>$redirection]);
    }

    #[Route('/private_group/edit/{id}', name: 'app_private_group_edit')]
    public function edit(PrivateGroup $privateGroup, Request $request): Response{
        $form = $this->createForm(PrivateGroupType::class, $privateGroup);
        $form->setData($privateGroup);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            /** @var PrivateGroup $privateGroup */
            $privateGroup = $form->getData();
            $this->entityManager->persist($privateGroup);
            $this->entityManager->flush();
            $this->addFlash('success', 'Groupe privé (' . $privateGroup->getName() . ') modifié');

            return $this->redirectToRoute('app_private_group_details', ['id' => $privateGroup->getId()]);
        }

        return $this->render('private_group/edit.html.twig', ['form'=>$form, 'group'=>$privateGroup]);
    }

    #[Route('/private_group/remove/{id}', name: 'app_private_group_delete')]
    public function delete(PrivateGroup $privateGroup): Response{
        if(!$this->privateGroupService->isOwner($this->getUser(), $privateGroup))
            throw new AccessDeniedException('Vous ne pouvez pas supprimer ce groupe privé car vous n\'en êtes pas le créateur.');

        $this->entityManager->remove($privateGroup);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_main');
    }
}
