<?php
namespace App\Doctrine;

use App\Entity\Ticket;
use Symfony\Component\Security\Core\Security;

class TicketSetOwnerListener
{
    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function prePersist(Ticket $ticket)
    {
        if ($ticket->getOwner()) {
            return;
        }
        if ($this->security->getUser()) {
            $ticket->setOwner($this->security->getUser());
        }
    }


}
