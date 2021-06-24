<?php

namespace App\Security\Voter;

use App\Entity\Transaction;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class TransactionVoter extends Voter
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
        return in_array($attribute, ['TRANSACTION_VIEW', 'TRANSACTION_EDIT'])
            && $subject instanceof \App\Entity\Transaction;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {

        $user = $token->getUser();
 
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Transaction $subject */

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'TRANSACTION_VIEW':
            case 'TRANSACTION_EDIT':
                if ($subject->getOwner() === $user) {
                    return true;
                }
                if ($this->security->isGranted('ROLE_ADMIN')) {
                    //return true;
                }
                return false;
        }

        throw new \Exception(sprintf('Unhandled attribute "%s"', $attribute));
    }
}
