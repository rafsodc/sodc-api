<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

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
        $user = $data->getOwner();
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
