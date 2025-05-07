<?php

namespace App\Security\Voter;

use App\Entity\Basket;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class BasketVoter extends Voter
{
    public const VIEW = 'BASKET_VIEW';
    public const EDIT = 'BASKET_EDIT';
    public const DELETE = 'BASKET_DELETE';

    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof Basket;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Basket $basket */
        $basket = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($basket, $user),
            self::EDIT => $this->canEdit($basket, $user),
            self::DELETE => $this->canDelete($basket, $user),
            default => false,
        };
    }

    private function canView(Basket $basket, UserInterface $user): bool
    {
        return $user instanceof User && (
            $basket->getOwner() === $user ||
            in_array('ROLE_ADMIN', $user->getRoles())
        );
    }

    private function canEdit(Basket $basket, UserInterface $user): bool
    {
        return $user instanceof User && (
            $basket->getOwner() === $user ||
            in_array('ROLE_ADMIN', $user->getRoles())
        );
    }

    private function canDelete(Basket $basket, UserInterface $user): bool
    {
        return $user instanceof User && (
            $basket->getOwner() === $user ||
            in_array('ROLE_ADMIN', $user->getRoles())
        );
    }
}
