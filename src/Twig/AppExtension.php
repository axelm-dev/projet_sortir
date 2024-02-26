<?php

namespace App\Twig;

use App\Entity\Meeting;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

#[AutoconfigureTag('twig.extension')]
class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('isButtonDisplayed', [$this, 'isButtonDisplayed']),
        ];
    }

    public function isButtonDisplayed(Meeting $meeting): bool
    {
        $dateNow = new \DateTime('now');
        return $meeting->getState()->getValue() === 'Ouverte' || ($meeting->getState()->getValue() ===  'Clôturée' && $dateNow < $meeting->getLimitDate());
    }
}