<?php

namespace App\Service;
use App\Entity\User as AppUser;
use App\Controller\PermAppInterface;
use App\Controller\ProjectController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthorizationService implements PermAppInterface
{
    private AuthorizationCheckerInterface $authorizationChecker;


    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function hasAccess($attributes, $user, $object = null): bool
    {
        return match ($attributes) {
            'EDIT_USER' => $this->canEditUser($user, $object),
            // MEETING
            self::PERM_MEETING_EDIT => $this->canEditMeeting($user, $object),
            self::PERM_MEETING_VIEW => $this->canViewMeeting($user, $object),
            self::PERM_MEETING_CANCEL => $this->canCancelMeeting($user, $object),
            self::PERM_MEETING_PUBLISH => $this->canPublishMeeting($user,$object),
            self::PERM_MEETING_NEW => $this->canNewMeeting($user, $object),
            self::PERM_MEETING_DELETE => $this->canDeleteMeeting($user, $object),
            // PLACE
            self::PERM_PLACE_EDIT => $this->canEditPlace($user, $object),
            self::PERM_PLACE_DELETE => $this->canDeletePlace($user, $object),
            self::PERM_PLACE_VIEW => $this->canViewPlace($user, $object),
            self::PERM_PLACE_NEW => $this->canNewPlace($user, $object),

            default => false,
        };
    }

    private function canEditUser($user): bool
    {
        if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return false;
        }

        if ($user->getId() === $this->authorizationChecker->getUser()->getId()) {
            return true;
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    private function canEditMeeting($user, $meeting): bool
    {
        if($user instanceof AppUser) {
            return true;
        }

        if($meeting->getOrganizer()->getId() === $this->authorizationChecker->getUser()->getId()) {
            return true;
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    private function canViewMeeting($user): bool
    {
       if($user instanceof AppUser) {
           return true;
       }

       return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    private function canCancelMeeting($user, $meeting): bool
    {
        if($user instanceof AppUser) {
            return true;
        }

        if($meeting->getOrganizer()->getId() === $this->authorizationChecker->getUser()->getId()) {
            return true;
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    private function canPublishMeeting($user, $meeting): bool
    {
        if($user instanceof AppUser) {
            return true;
        }

        if($meeting->getOrganizer()->getId() === $this->authorizationChecker->getUser()->getId()) {
            return true;
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    private function canNewMeeting($user, $meeting): bool
    {
        if($user instanceof AppUser) {
            return true;
        }

        return true;
    }

    private function canDeleteMeeting($user, $object): bool
    {
        if($user instanceof AppUser) {
            return true;
        }

        if($object->getOrganizer()->getId() === $this->authorizationChecker->getUser()->getId()) {
            return true;
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    private function canEditPlace($user, mixed $object): bool
    {
        if($user instanceof AppUser) {
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
        if($user instanceof AppUser) {
            return true;
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    private function canNewPlace($user, mixed $object): bool
    {
        if($user instanceof AppUser) {
            return true;
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }
}