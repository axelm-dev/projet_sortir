<?php

namespace App\Service;
use App\Controller\PermAndStateAppInterface;
use App\Entity\Meeting;
use App\Entity\User as AppUser;
use App\Controller\ProjectController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthorizationService implements PermAndStateAppInterface
{
    private AuthorizationCheckerInterface $authorizationChecker;

    private Security $security;
    private \DateTime $dateNow;


    public function __construct(AuthorizationCheckerInterface $authorizationChecker, Security $security)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->security = $security;
        $this->dateNow = new \DateTime('now');
    }

    public function hasAccess($attributes, $object = null): bool
    {
        return match ($attributes) {
            // MEETING
            self::PERM_MEETING_EDIT => $this->canEditMeeting($object),
            self::PERM_MEETING_VIEW => $this->canViewMeeting($object),
            self::PERM_MEETING_CANCEL => $this->canCancelMeeting($object),
            self::PERM_MEETING_PUBLISH => $this->canPublishMeeting($object),
            self::PERM_MEETING_NEW => $this->canNewMeeting($object),
            self::PERM_MEETING_DELETE => $this->canDeleteMeeting($object),
            self::PERM_MEETING_REGISTER => $this->canRegisterMeeting($object),
            self::PERM_MEETING_UNREGISTER => $this->canUnregisterMeeting($object),

            // PLACE
            self::PERM_PLACE_EDIT => $this->canEditPlace($object),
            self::PERM_PLACE_DELETE => $this->canDeletePlace($object),
            self::PERM_PLACE_VIEW => $this->canViewPlace($object),
            self::PERM_PLACE_NEW => $this->canNewPlace($object),

            default => false,
        };
    }


    private function canEditMeeting($meeting): bool
    {
        /**
         * @var Meeting $meeting
         */
        
        $user = $this->security->getUser();
        if($user && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            if($meeting->getOrganizer()->getId() === $user->getId()) {
                return $this->dateNow < $meeting->getLimitDate();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function canViewMeeting($user): bool
    {
        $user = $this->security->getUser();
       if($user && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
           return true;
       }

       return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    private function canCancelMeeting($meeting): bool
    {
        $user = $this->security->getUser();
        if($user) {
            if((($meeting->getOrganizer()->getId() === $user->getId()) || $this->authorizationChecker->isGranted('ROLE_ADMIN'))
                && $meeting->getState()->getValue() === self::STATE_MEETING_OPENED) {
                return $this->dateNow < $meeting->getLimitDate();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function canPublishMeeting($meeting): bool
    {
        /**
         * @var Meeting $meeting
         */
        $user = $this->security->getUser();
        if($user && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            if($meeting->getOrganizer()->getId() === $user->getId()) {
                if($meeting->getState()->getValue() === self::STATE_MEETING_OPENED) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    private function canNewMeeting($meeting): bool
    {
        $user = $this->security->getUser();
        if($user && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return true;
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    private function canDeleteMeeting($object): bool
    {
        $user = $this->security->getUser();
        if($user && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            if($object->getOrganizer()->getId() === $user->getId()) {
                return true;
            }
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }


    private function canRegisterMeeting($meeting): bool
    {
        $user = $this->security->getUser();
        if($user && !$meeting->getParticipants()->contains($user)) {
            if($meeting->getState()->getValue() === self::STATE_MEETING_OPENED || ($meeting->getState()->getValue() === self::STATE_MEETING_CLOSED && $this->dateNow < $meeting->getLimitDate())) {
                return true;
            } else {
                return false;
            }
        }else {
            return false;
        }
    }

    private function canUnregisterMeeting($meeting): bool
    {
        $user = $this->security->getUser();
        if($user && $meeting->getParticipants()->contains($user)) {
            if($meeting->getState()->getValue() === self::STATE_MEETING_OPENED || ($meeting->getState()->getValue() === self::STATE_MEETING_CLOSED && $this->dateNow < $meeting->getLimitDate())) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }



    private function canEditPlace($object): bool
    {
        $user = $this->security->getUser();
        if($user && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return true;
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    private function canDeletePlace($object): bool
    {
        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    private function canViewPlace($object): bool
    {
        $user = $this->security->getUser();
        if($user && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return true;
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    private function canNewPlace($object): bool
    {
        $user = $this->security->getUser();
        if($user && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return true;
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }


}