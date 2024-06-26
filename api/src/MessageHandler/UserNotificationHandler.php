<?php

namespace App\MessageHandler;

use App\Entity\UserNotification;
use App\Message\UserNotificationMessage as UserNotificationMessage;
use App\Repository\UserNotificationRepository;
use App\Service\NotifyClient;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Psr\Log\LoggerInterface;

class UserNotificationHandler implements MessageHandlerInterface
{
    private $userNotificationRepository;
    private $notifyClient;
    private $replyTo;
    private $logger;

    public function __construct(UserNotificationRepository $userNotificationRepository, NotifyClient $notifyClient, LoggerInterface $logger) 
    {
        $this->userNotificationRepository = $userNotificationRepository;
        $this->notifyClient = $notifyClient->client;
        $this->replyTo = $notifyClient->replyTos['secretary'];
        $this->logger = $logger;
    }

    public function __invoke(UserNotificationMessage $message)
    {
        $userNotification = $this->userNotificationRepository->find($message->getUserNotificationId());

        if (!$userNotification) {
            $this->logger->error('UserNotificationHandler: UserNotification not found.');
            return;
        }
        
        $user = $userNotification->getOwner();

        try {

            $this->notifyClient->sendEmail(
                $user->getEmail(),
                $userNotification->getTemplateId(),
                $userNotification->getData(),
                $userNotification->getId(),
                $this->replyTo
            );

            // Update the 'sent' status to true
            $userNotification->setSent(true);
            $this->userNotificationRepository->save($userNotification);
            $this->logger->info('UserNotificationHandler: Email sent and status updated for UserNotification ID: ' . $userNotification->getId());
        } catch (\Exception $e) {
            $this->logger->error('UserNotificationHandler: Failed to send email for UserNotification ID: ' . $userNotification->getId() . '. Error: ' . $e->getMessage());
        }
    }
}
