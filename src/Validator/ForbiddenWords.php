<?php

namespace App\Validator;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

/**
 * Interdit la présence de certains mots dans un texte (ex. : "spam" dans le titre).
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class ForbiddenWords extends Constraint
{
    public string $message = 'Le mot "{{ word }}" est interdit.';

    /** @var string[] */
    public array $words = ['spam'];

    /**
     * @param string[]|null $words
     */
    #[HasNamedArguments]
    public function __construct(?array $words = null, ?string $message = null, ?array $groups = null, mixed $payload = null)
    {
        parent::__construct(null, $groups, $payload);

        $this->words = $words ?? $this->words;
        $this->message = $message ?? $this->message;
    }
}
