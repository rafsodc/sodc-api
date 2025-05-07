<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{
    public const VIEW = 'USER_VIEW';
    public const EDIT = 'USER_EDIT';

    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        /** @var User $targetUser */
        $targetUser = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($targetUser, $user),
            self::EDIT => $this->canEdit($targetUser, $user),
            default => false,
        };
    }

    private function canView(User $targetUser, UserInterface $user): bool
    {
        // Currently allowing all authenticated users to view any user
        // Uncomment the following code to restrict viewing to self or shared profiles
        /*
        if ($targetUser->getIsShared()) {
            return true;
        }
        return $targetUser === $user;
        */
        return true;
    }

    private function canEdit(User $targetUser, UserInterface $user): bool
    {
        return $targetUser === $user;
    }
}
