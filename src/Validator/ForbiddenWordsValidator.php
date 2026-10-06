<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ForbiddenWordsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ForbiddenWords) {
            throw new UnexpectedTypeException($constraint, ForbiddenWords::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!\is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        foreach ($constraint->words as $word) {
            // Recherche insensible à la casse ("Spam", "SPAM"...)
            if (false !== mb_stripos($value, $word)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ word }}', $word)
                    ->addViolation();

                return;
            }
        }
    }
}
