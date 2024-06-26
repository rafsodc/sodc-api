<?php

namespace App\MessageHandler;

use App\Entity\NotifyMessageUser;
use App\Message\NotifyMessageUser as NotifyMessageUserMessage;
use App\Repository\NotifyMessageUserRepository;
use App\Service\NotifyClient;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NotifyMessageUserHandler implements MessageHandlerInterface
{
    private $notifyMessageUserRepository;
    private $notifyClient;
    private $replyTo;

    public function __construct(NotifyMessageUserRepository $notifyMessageUserRepository, NotifyClient $notifyClient) 
    {
        $this->notifyMessageUserRepository = $notifyMessageUserRepository;
        $this->notifyClient = $notifyClient->client;
        $this->replyTo = $notifyClient->replyTos['secretary'];
    }

    public function __invoke(NotifyMessageUserMessage $message)
    {
        $notifyMessageUser = $this->notifyMessageUserRepository->find($message->getNotifyMessageUserId());

        if (!$notifyMessageUser) {
            return;
        }
        
        $user = $notifyMessageUser->getOwner();

        $this->notifyClient->sendEmail(
            $user->getEmail(),
            $notifyMessageUser->getNotifyMessage()->getTemplateId(),
            $notifyMessageUser->getData(),
            $notifyMessageUser->getId(),
            $this->replyTo
        );

        // Update the 'sent' status to true
        $notifyMessageUser->setSent(true);
        $this->notifyMessageUserRepository->save($notifyMessageUser);
    }
}
