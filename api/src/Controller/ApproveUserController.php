<?php
// api/src/Controller/CreateBookPublication.php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\UserApprove;
use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApproveUserController
{
    private $entityManager;
    private $messageBus;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $messageBus, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
        $this->validator = $validator;
    }

    public function __invoke(User $data): User
    {
        $errors = $this->validator->validate($data, null, 'approve_user');
        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }
        $data->addRole("ROLE_USER");
        $message = new UserApprove($data->getId());
        $this->messageBus->dispatch($message);

        return $data;
    }
}
