<?php
namespace App\Doctrine;

use App\Entity\Basket;
use App\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class BasketListener
{
    private $security;
    private $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    public function prePersist(Basket $basket)
    {
        $this->removeBaskets($basket);   
        $this->setOwner($basket);
        $this->setTickets($basket);
    }

    public function preRemove(Basket $basket)
    {
        $this->removeTickets($basket);
    }

    function removeBaskets(Basket $basket)
    {
        $baskets = $this->entityManager->getRepository(Basket::class)->findBy(['owner' => $basket->getOwner(), 'event' => $basket->getEvent(), 'transaction' => null]);
        foreach($baskets as $basket) {
            $this->entityManager->remove($basket);
        }
        $this->entityManager->flush();
    }

    function setOwner(Basket $basket)
    {
        if ($basket->getOwner()) {
            return;
        }
        if ($this->security->getUser()) {
            $basket->setOwner($this->security->getUser());
        }
    }

    function setTickets(Basket $basket)
    {
        $tickets = $this->entityManager->getRepository(Ticket::class)->findBy(['owner' => $basket->getOwner(), 'event' => $basket->getEvent(), 'paid' => false]);
        /** @var Ticket $ticket */
        foreach($tickets as $ticket) {
            $basket->addTicket($ticket);
        }
    }

    function removeTickets(Basket $basket)
    {
        $tickets = $basket->getTickets();
        foreach($tickets as $ticket) {
            $basket->removeTicket($ticket);
        }
    }

}
