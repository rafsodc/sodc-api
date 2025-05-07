<?php

namespace App\Security\Voter;

use App\Entity\Ticket;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class TicketVoter extends Voter
{
    public const VIEW = 'TICKET_VIEW';
    public const EDIT = 'TICKET_EDIT';
    public const DELETE = 'TICKET_DELETE';

    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof Ticket;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Ticket $ticket */
        $ticket = $subject;

        return match($attribute) {
            self::VIEW => $this->canView($ticket, $user),
            self::EDIT => $this->canEdit($ticket, $user),
            self::DELETE => $this->canDelete($ticket, $user),
            default => false,
        };
    }

    private function canView(Ticket $ticket, UserInterface $user): bool
    {
        return $user instanceof User && (
            $ticket->getOwner() === $user ||
            in_array('ROLE_ADMIN', $user->getRoles())
        );
    }

    private function canEdit(Ticket $ticket, UserInterface $user): bool
    {
        return $user instanceof User && (
            $ticket->getOwner() === $user ||
            in_array('ROLE_ADMIN', $user->getRoles())
        );
    }

    private function canDelete(Ticket $ticket, UserInterface $user): bool
    {
        return $user instanceof User && (
            $ticket->getOwner() === $user ||
            in_array('ROLE_ADMIN', $user->getRoles())
        );
    }
}
