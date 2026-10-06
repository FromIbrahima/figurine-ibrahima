<?php

namespace App\Security;

use App\Entity\Figurine;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Un utilisateur ne peut modifier ou supprimer que ses propres figurines.
 *
 * @extends Voter<string, Figurine>
 */
class FigurineVoter extends Voter
{
    public const EDIT = 'FIGURINE_EDIT';
    public const DELETE = 'FIGURINE_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [self::EDIT, self::DELETE], true)
            && $subject instanceof Figurine;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Figurine $subject */
        return $subject->getOwner()?->getId() === $user->getId();
    }
}
