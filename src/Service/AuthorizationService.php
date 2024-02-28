<?php

namespace App\Service;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthorizationService
{
    private AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function hasAccess($attributes, $object = null): bool
    {
        switch ($attributes) {
            case 'EDIT-USER':
                return $this->canEditUser($object);
            case 'EDIT-MEETING':
                return $this->canEditMeeting($object);
            case 'VIEW-MEETING':
                return $this->canViewMeeting($object);
            case 'CANCEL-MEETING':
                return $this->canCancelMeeting($object);
            case 'PUBLISH-MEETING':
                return $this->canPublishMeeting($object);
            case 'NEW-MEETING':
                return $this->canNewMeeting($object);
            case 'DELETE-MEETING':
                return $this->canDeleteMeeting($object);

            default:
                return false;
        }
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

    private function canEditMeeting($meeting): bool
    {
        if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return false;
        }

        if($meeting->getOrganizer()->getId() === $this->authorizationChecker->getUser()->getId()) {
            return true;
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    private function canViewMeeting($meeting): bool
    {
        if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return false;
        }




        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    private function canCancelMeeting($meeting): bool
    {
        if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return false;
        }

        if($meeting->getOrganizer()->getId() === $this->authorizationChecker->getUser()->getId()) {
            return true;
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    private function canPublishMeeting($meeting): bool
    {
        if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return false;
        }

        if($meeting->getOrganizer()->getId() === $this->authorizationChecker->getUser()->getId()) {
            return true;
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    private function canNewMeeting($meeting): bool
    {
        if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return false;
        }

        return true;
    }

    private function canDeleteMeeting(mixed $object): bool
    {
        if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return false;
        }

        if($object->getOrganizer()->getId() === $this->authorizationChecker->getUser()->getId()) {
            return true;
        }

        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }
}