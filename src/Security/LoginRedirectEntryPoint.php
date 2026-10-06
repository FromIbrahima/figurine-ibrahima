<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

/**
 * Appelé lorsqu'un visiteur non connecté tente d'accéder à une page protégée
 * (liste des figurines, profil...) : redirection vers /login avec un message d'erreur.
 */
class LoginRedirectEntryPoint implements AuthenticationEntryPointInterface
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function start(Request $request, ?AuthenticationException $authException = null): RedirectResponse
    {
        $session = $request->hasSession() ? $request->getSession() : null;

        if ($session instanceof FlashBagAwareSessionInterface) {
            $session->getFlashBag()->add('danger', 'Vous devez être connecté pour accéder à cette page.');
        }

        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}
