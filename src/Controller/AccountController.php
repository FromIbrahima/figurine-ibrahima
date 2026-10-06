<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Profil de l'utilisateur connecté : affichage et modification.
 */
#[Route('/account')]
#[IsGranted('ROLE_USER')]
final class AccountController extends AbstractController
{
    #[Route('', name: 'app_account', methods: ['GET'])]
    public function show(#[CurrentUser] User $user): Response
    {
        return $this->render('account/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/edit', name: 'app_account_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, #[CurrentUser] User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Votre profil a bien été modifié.');

            return $this->redirectToRoute('app_account');
        }

        if ($form->isSubmitted()) {
            // On annule les modifications non valides pour ne pas afficher de données erronées (navbar...)
            $entityManager->refresh($user);
            $this->addFlash('danger', 'Le profil n\'a pas pu être modifié : veuillez corriger les erreurs du formulaire.');
        }

        return $this->render('account/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ], new Response(null, $form->isSubmitted() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK));
    }
}
