<?php
namespace App\Doctrine;

use App\Entity\Transaction;
use App\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class TransactionListener
{
    private $security;
    private $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function prePersist(Transaction $transaction)
    {
        $this->setOwner($transaction);
        $this->setTickets($transaction);
    }

    public function preRemove(Transaction $transaction)
    {
        $this->removeTickets($transaction);
    }

    function setOwner(Transaction $transaction)
    {
        if ($transaction->getOwner()) {
            return;
        }
        if ($this->security->getUser()) {
            $transaction->setOwner($this->security->getUser());
        }
    }

    function setTickets(Transaction $transaction)
    {
        $tickets = $this->entityManager->getRepository(Ticket::class)->findBy(['owner' => $transaction->getOwner(), 'event' => $transaction->getEvent(), 'paid' => false]);
        /** @var Ticket $ticket */
        foreach($tickets as $ticket) {
            $transaction->addTicket($ticket);
        }
    }

    function removeTickets(Transaction $transaction)
    {
        $tickets = $transaction->getTickets();
        foreach($tickets as $ticket) {
            $transaction->removeTicket($ticket);
        }
    }

}
