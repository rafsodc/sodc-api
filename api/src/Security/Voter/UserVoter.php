<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{
    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['USER_VIEW', 'USER_EDIT'])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'USER_VIEW':
                /*if($user->getIsShared()) {
                    return true;
                }
                if ($subject === $user) {
                    return true;
                }
                return false;*/
                return true;
            case 'USER_EDIT':
                if ($subject === $user) {
                    return true;
                }
                return false;
        }

        throw new \Exception(sprintf('Unhandled attribute "%s"', $attribute));
    }
}
