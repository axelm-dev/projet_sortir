<?php

namespace App\Service;
use App\Controller\PermAndStateAppInterface;
use App\Entity\User as AppUser;
use App\Controller\ProjectController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthorizationService implements PermAndStateAppInterface
{
    private AuthorizationCheckerInterface $authorizationChecker;

    private Security $security;


    public function __construct(AuthorizationCheckerInterface $authorizationChecker, Security $security)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->security = $security;

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
            self::PERM_PLACE_EDIT => $this->canEditPlace($user, $object),
            self::PERM_PLACE_DELETE => $this->canDeletePlace($user, $object),
            self::PERM_PLACE_VIEW => $this->canViewPlace($user, $object),
            self::PERM_PLACE_NEW => $this->canNewPlace($user, $object),

            default => false,
        };
    }


    private function canEditMeeting($meeting): bool
    {
        $user = $this->security->getUser();
        if($user && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            if($meeting->getOrganizer()->getId() === $this->authorizationChecker->getUser()->getId()) {
                return true;
            }
        }
        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
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
        if($user && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            if($meeting->getOrganizer()->getId() === $this->authorizationChecker->getUser()->getId()) {
                return true;
            }
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    private function canPublishMeeting($meeting): bool
    {
        $user = $this->security->getUser();
        if($user && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            if($meeting->getOrganizer()->getId() === $this->authorizationChecker->getUser()->getId()) {
                return true;
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
            if($object->getOrganizer()->getId() === $this->authorizationChecker->getUser()->getId()) {
                return true;
            }
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }


    private function canRegisterMeeting($meeting): bool
    {
        $user = $this->security->getUser();
        if($user instanceof AppUser && !$meeting->getParticipants()->contains($user)) {
            return true;
        }else {
            return false;
        }
    }

    private function canUnregisterMeeting($meeting): bool
    {
        $user = $this->security->getUser();
        if(($user instanceof AppUser) && $meeting->getParticipants()->contains($user)) {
            return true;
        } else {
            return false;
        }
    }



    private function canEditPlace($user, mixed $object): bool
    {
        if($user instanceof AppUser && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return true;
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    private function canDeletePlace($user, mixed $object): bool
    {
        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    private function canViewPlace($user, mixed $object): bool
    {
        if($user instanceof AppUser && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return true;
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    private function canNewPlace($user, mixed $object): bool
    {
        if($user instanceof AppUser && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return true;
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }


}