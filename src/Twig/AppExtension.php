<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Filtres Twig de l'application :
 *  - time_ago : « il y a 3 jours »
 *  - price    : « 49,90 € »
 *  - truncate : coupe un texte à N caractères (« Titre très long… »)
 */
class AppExtension extends AbstractExtension
{
    private const TIME_UNITS = [
        31536000 => ['an', 'ans'],
        2592000 => ['mois', 'mois'],
        604800 => ['semaine', 'semaines'],
        86400 => ['jour', 'jours'],
        3600 => ['heure', 'heures'],
        60 => ['minute', 'minutes'],
    ];

    public function getFilters(): array
    {
        return [
            new TwigFilter('time_ago', $this->timeAgo(...)),
            new TwigFilter('price', $this->formatPrice(...)),
            new TwigFilter('truncate', $this->truncate(...)),
        ];
    }

    public function timeAgo(?\DateTimeInterface $date): string
    {
        if (null === $date) {
            return '';
        }

        $seconds = time() - $date->getTimestamp();

        if ($seconds < 60) {
            return 'à l\'instant';
        }

        foreach (self::TIME_UNITS as $unitSeconds => [$singular, $plural]) {
            if ($seconds >= $unitSeconds) {
                $count = intdiv($seconds, $unitSeconds);

                return sprintf('il y a %d %s', $count, $count > 1 ? $plural : $singular);
            }
        }

        return 'à l\'instant';
    }

    public function formatPrice(string|float|int|null $price): string
    {
        // Espace insécable entre les milliers et avant le symbole €
        return number_format((float) $price, 2, ',', "\u{202F}")."\u{00A0}€";
    }

    public function truncate(?string $text, int $length = 30, string $suffix = '…'): string
    {
        $text = (string) $text;

        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $length)).$suffix;
    }
}
