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
        return in_array($attribute, ['TICKET_GET', 'TICKET_PATCH', 'TICKET_POST'])
            && $subject instanceof \App\Entity\Ticket;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {

        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Ticket $subject **/

        // Check that the booking is open
        

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'TICKET_GET':
            case 'TICKET_PATCH':
            case 'TICKET_POST':
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
