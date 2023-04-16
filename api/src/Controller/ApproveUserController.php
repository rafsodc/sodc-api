<?php
// api/src/Controller/CreateBookPublication.php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\UserApprove;

class ApproveUserController
{
    private $entityManager;
    private $messageBus;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $messageBus)
    {
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
    }

    public function __invoke(User $data): User
    {
        $data->addRole("ROLE_USER");
        $message = new UserApprove($data->getId());
        $this->messageBus->dispatch($message);

        return $data;
    }
}
