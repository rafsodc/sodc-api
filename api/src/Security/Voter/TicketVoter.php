<?php

namespace App\Security\Voter;

use App\Entity\Ticket;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class TicketVoter extends Voter
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
        return in_array($attribute, ['TICKET_VIEW', 'TICKET_EDIT', 'TICKET_DELETE'])
            && $subject instanceof \App\Entity\Ticket;
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
            case 'TICKET_VIEW':
            case 'TICKET_EDIT':
                if ($subject->getOwner() === $user) {
                    return true;
                }
            case 'TICKET_DELETE':
                if ($subject->getOwner() === $user && !$subject->getPaid()) {
                     return true;
                }
        }
        return false;

        throw new \Exception(sprintf('Unhandled attribute "%s"', $attribute));
    }
}
