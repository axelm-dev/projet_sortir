<?php

namespace App\Twig;

use App\Controller\PermAndStateAppInterface;
use App\Entity\Meeting;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

#[AutoconfigureTag('twig.extension')]
class AppExtension extends AbstractExtension implements PermAndStateAppInterface
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
        return $meeting->getState()->getValue() === self::STATE_MEETING_OPENED || ($meeting->getState()->getValue() ===  self::STATE_MEETING_CLOSED && $dateNow < $meeting->getLimitDate());
    }

    public function isButtonModifyAndCancelDisplayed(Meeting $meeting) : bool
    {
        $dateNow = new \DateTime('now');
        return $dateNow < $meeting->getLimitDate();
    }
}