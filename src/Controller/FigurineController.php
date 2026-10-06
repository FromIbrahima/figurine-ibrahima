<?php

namespace App\Controller;

use App\Entity\Figurine;
use App\Entity\User;
use App\Form\FigurineType;
use App\Repository\FigurineRepository;
use App\Security\FigurineVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Gestion des figurines : liste, création, affichage, modification et suppression.
 * Toutes les pages sont réservées aux utilisateurs connectés.
 */
#[Route('/figurine')]
#[IsGranted('ROLE_USER')]
final class FigurineController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('', name: 'app_figurine_index', methods: ['GET'])]
    public function index(FigurineRepository $figurineRepository): Response
    {
        return $this->render('figurine/index.html.twig', [
            'figurines' => $figurineRepository->findAllWithOwner(),
        ]);
    }

    #[Route('/created', name: 'app_figurine_create', methods: ['GET', 'POST'])]
    public function create(Request $request, #[CurrentUser] User $user): Response
    {
        $figurine = new Figurine();
        $figurine->setOwner($user);

        $form = $this->createForm(FigurineType::class, $figurine, [
            'image_required' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($figurine);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre figurine a bien été publiée !');

            return $this->redirectToRoute('app_figurine_show', ['id' => $figurine->getId()]);
        }

        if ($form->isSubmitted()) {
            $this->addFlash('danger', 'La figurine n\'a pas pu être publiée : veuillez corriger les erreurs du formulaire.');
        }

        return $this->render('figurine/create.html.twig', [
            'form' => $form,
        ], new Response(null, $form->isSubmitted() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK));
    }

    #[Route('/{id}', name: 'app_figurine_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(#[MapEntity(id: 'id')] Figurine $figurine): Response
    {
        return $this->render('figurine/show.html.twig', [
            'figurine' => $figurine,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_figurine_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, #[MapEntity(id: 'id')] Figurine $figurine): Response
    {
        if (!$this->isGranted(FigurineVoter::EDIT, $figurine)) {
            $this->addFlash('danger', 'Vous ne pouvez modifier que vos propres figurines.');

            return $this->redirectToRoute('app_figurine_show', ['id' => $figurine->getId()]);
        }

        $form = $this->createForm(FigurineType::class, $figurine, [
            'image_required' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre figurine a bien été modifiée.');

            return $this->redirectToRoute('app_figurine_show', ['id' => $figurine->getId()]);
        }

        if ($form->isSubmitted()) {
            $this->addFlash('danger', 'La figurine n\'a pas pu être modifiée : veuillez corriger les erreurs du formulaire.');
        }

        return $this->render('figurine/edit.html.twig', [
            'figurine' => $figurine,
            'form' => $form,
        ], new Response(null, $form->isSubmitted() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK));
    }

    #[Route('/{id}/delete', name: 'app_figurine_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, #[MapEntity(id: 'id')] Figurine $figurine): Response
    {
        if (!$this->isGranted(FigurineVoter::DELETE, $figurine)) {
            $this->addFlash('danger', 'Vous ne pouvez supprimer que vos propres figurines.');

            return $this->redirectToRoute('app_figurine_show', ['id' => $figurine->getId()]);
        }

        if (!$this->isCsrfTokenValid('delete'.$figurine->getId(), $request->getPayload()->getString('_token'))) {
            $this->addFlash('danger', 'Jeton de sécurité invalide, la figurine n\'a pas été supprimée.');

            return $this->redirectToRoute('app_figurine_show', ['id' => $figurine->getId()]);
        }

        $this->entityManager->remove($figurine);
        $this->entityManager->flush();

        // Message de suppression affiché en rouge
        $this->addFlash('danger', 'La figurine a bien été supprimée.');

        return $this->redirectToRoute('app_figurine_index', [], Response::HTTP_SEE_OTHER);
    }
}
