<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Doctrine\Common\Collections\Criteria;

class TransactionDataPersister implements DataPersisterInterface
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function supports($data): bool
    {
        return $data instanceof Transaction;
    }

    /**
     * @param Transaction $data
     */
    public function persist($data)
    {
        // Set up a criteria for the getTickets request - see https://symfonycasts.com/screencast/collections/criteria-collection-filtering#play
        $ticketCriteria = Criteria::create()->andWhere(Criteria::expr()->eq('paid', false));
        $tickets = $data->getOwner()->getTickets()->matching($ticketCriteria);
        foreach($tickets as $ticket) {
            $data->addTicket($ticket);
        }

        dd($data);
        //$this->entityManager->persist($data);
        //$this->entityManager->flush();
    }

    public function remove($data)
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}
