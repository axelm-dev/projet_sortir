<?php

namespace App\Twig;

use App\Controller\PermAndStateAppInterface;
use App\Entity\Meeting;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

#[AutoconfigureTag('twig.extension')]
class AppExtension extends AbstractExtension implements PermAndStateAppInterface
{

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function getFunctions(): array
    {
        return [
            new TwigFunction('isButtonsInscriptionDisplayed', [$this, 'isButtonsInscriptionDisplayed']),
            new TwigFunction('isButtonCancelDisplayed', [$this, 'isButtonCancelDisplayed']),
            new TwigFunction('isButtonModifyDisplayed', [$this, 'isButtonModifyDisplayed']),
        ];
    }

    public function isButtonsInscriptionDisplayed(Meeting $meeting): bool
    {
        $dateNow = new \DateTime('now');
        return $meeting->getState()->getValue() === self::STATE_MEETING_OPENED || ($meeting->getState()->getValue() ===  self::STATE_MEETING_CLOSED && $dateNow < $meeting->getLimitDate());
    }

    public function isButtonCancelDisplayed(Meeting $meeting) : bool
    {
        $dateNow = new \DateTime('now');
        return ($dateNow < $meeting->getLimitDate()) || $this->security->isGranted('ROLE_ADMIN');
    }

    public function isButtonModifyDisplayed(Meeting $meeting): bool
    {
        $user = $this->security->getUser();
        $dateNow = new \DateTime('now');
        return ($meeting->getOrganizer()->getId() === $user->getId() ) && ($dateNow < $meeting->getLimitDate());
    }
}