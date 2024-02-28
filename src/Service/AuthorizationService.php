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
        return match ($attributes) {
            'EDIT_USER' => $this->canEditUser($object),
            'EDIT_MEETING' => $this->canEditMeeting($object),
            'VIEW_MEETING' => $this->canViewMeeting($object),
            'CANCEL_MEETING' => $this->canCancelMeeting($object),
            'PUBLISH_MEETING' => $this->canPublishMeeting($object),
            'NEW_MEETING' => $this->canNewMeeting($object),
            'DELETE_MEETING' => $this->canDeleteMeeting($object),
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