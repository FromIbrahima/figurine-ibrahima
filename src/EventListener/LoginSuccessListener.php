<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

/**
 * Affiche « Bienvenue prénom » (message bleu) après une connexion réussie.
 */
#[AsEventListener(event: LoginSuccessEvent::class)]
final class LoginSuccessListener
{
    public function __invoke(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        $request = $event->getRequest();

        if (!$user instanceof User || !$request->hasSession()) {
            return;
        }

        $session = $request->getSession();

        if ($session instanceof FlashBagAwareSessionInterface) {
            $session->getFlashBag()->add('info', sprintf('Bienvenue %s !', $user->getFirstname()));
        }
    }
}
