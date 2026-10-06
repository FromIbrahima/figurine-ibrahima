<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;

/**
 * L'avatar par défaut est partagé par tous les nouveaux utilisateurs :
 * VichUploader ne doit jamais le supprimer du disque quand un utilisateur
 * change d'image de profil ou supprime son compte.
 */
#[AsEventListener(event: Events::PRE_REMOVE)]
final class DefaultAvatarProtectionListener
{
    public function __invoke(Event $event): void
    {
        $object = $event->getObject();

        if ($object instanceof User && User::DEFAULT_IMAGE === $object->getImageName()) {
            $event->cancel();
        }
    }
}
