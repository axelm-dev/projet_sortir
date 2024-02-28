<?php

namespace App\Service;
use App\Entity\User as AppUser;
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

    public function hasAccess($attributes, $user, $object = null): bool
    {
        return match ($attributes) {
            'EDIT_USER' => $this->canEditUser($user, $object),
            'EDIT_MEETING' => $this->canEditMeeting($user, $object),
            self::PERM_MEETING_VIEW => $this->canViewMeeting($user, $object),
            self::PERM_MEETING_CANCEL => $this->canCancelMeeting($user, $object),
            'PUBLISH_MEETING' => $this->canPublishMeeting($user,$object),
            'NEW_MEETING' => $this->canNewMeeting($user, $object),
            'DELETE_MEETING' => $this->canDeleteMeeting($user, $object),
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
        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY') === false) {
            return false;
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