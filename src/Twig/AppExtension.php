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
            new TwigFunction('isButtonsInscriptionDisplayed', [$this, 'isButtonsInscriptionDisplayed']),
            new TwigFunction('isButtonModifyAndCancelDisplayed', [$this, 'isButtonModifyAndCancelDisplayed']),
        ];
    }

    public function isButtonsInscriptionDisplayed(Meeting $meeting): bool
    {
        $dateNow = new \DateTime('now');
        return $meeting->getState()->getValue() === 'Ouverte' || ($meeting->getState()->getValue() ===  'Clôturée' && $dateNow < $meeting->getLimitDate());
    }

    public function isButtonModifyAndCancelDisplayed(Meeting $meeting) : bool
    {
        $dateNow = new \DateTime('now');
        return $dateNow < $meeting->getLimitDate();
    }
}