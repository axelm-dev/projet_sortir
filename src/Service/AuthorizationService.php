<?php

namespace App\Service;

use App\Controller\PermAppInterface;
use App\Controller\ProjectController;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthorizationService implements PermAppInterface
{
    private AuthorizationCheckerInterface $authorizationChecker;

    private ProjectController $projectController;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function hasAccess($attributes, $object = null): bool
    {
        return match ($attributes) {
            'EDIT_USER' => $this->canEditUser($object),
            'EDIT_MEETING' => $this->canEditMeeting($object),
            self::PERM_MEETING_VIEW => $this->canViewMeeting($object),
            self::PERM_MEETING_CANCEL => $this->canCancelMeeting($object),
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