<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Un utilisateur déjà connecté n'a rien à faire sur la page de connexion
        if ($this->getUser()) {
            return $this->redirectToRoute('app_figurine_index');
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        // Cette méthode n'est jamais exécutée : la déconnexion est interceptée par le pare-feu (security.yaml).
        throw new \LogicException('Cette méthode est interceptée par la clé "logout" du pare-feu.');
    }
}
