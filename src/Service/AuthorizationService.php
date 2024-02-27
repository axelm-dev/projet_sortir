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
}