<?php

namespace App\Security\Voter;

use App\Entity\Transaction;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class TransactionVoter extends Voter
{
    public const VIEW = 'TRANSACTION_VIEW';
    public const EDIT = 'TRANSACTION_EDIT';
    public const DELETE = 'TRANSACTION_DELETE';

    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof Transaction;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Transaction $transaction */
        $transaction = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($transaction, $user),
            self::EDIT => $this->canEdit($transaction, $user),
            self::DELETE => $this->canDelete($transaction, $user),
            default => false,
        };
    }

    private function canView(Transaction $transaction, UserInterface $user): bool
    {
        return $user instanceof User && (
            $transaction->getBasket()->getOwner() === $user ||
            in_array('ROLE_ADMIN', $user->getRoles())
        );
    }

    private function canEdit(Transaction $transaction, UserInterface $user): bool
    {
        return $user instanceof User && (
            $transaction->getBasket()->getOwner() === $user ||
            in_array('ROLE_ADMIN', $user->getRoles())
        );
    }

    private function canDelete(Transaction $transaction, UserInterface $user): bool
    {
        return $user instanceof User && (
            $transaction->getBasket()->getOwner() === $user ||
            in_array('ROLE_ADMIN', $user->getRoles())
        );
    }
}
