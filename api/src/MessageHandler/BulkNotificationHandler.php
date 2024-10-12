<?php

namespace App\MessageHandler;

//use App\Entity\bulkNotification;
use App\Entity\UserNotification;
use App\Entity\User;
use App\Message\BulkNotificationMessage;
use App\Message\UserNotificationMessage;
use App\Repository\BulkNotificationRepository;
use App\Repository\UserNotificationRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;
use App\Repository\TicketRepository;

class BulkNotificationHandler implements MessageHandlerInterface
{
    private $bulkNotificationRepository;
    private $userNotificationRepository;
    private $messageBus;
    private $logger;
    private $ticketRepository;

    public function __construct(
        BulkNotificationRepository $bulkNotificationRepository,
        UserNotificationRepository $userNotificationRepository,
        TicketRepository $ticketRepository,
        MessageBusInterface $messageBus,
        LoggerInterface $logger
    ) {
        $this->bulkNotificationRepository = $bulkNotificationRepository;
        $this->userNotificationRepository = $userNotificationRepository;
        $this->ticketRepository = $ticketRepository;
        $this->messageBus = $messageBus;
        $this->logger = $logger;
    }

    public function __invoke(BulkNotificationMessage $message)
    {
        $bulkNotification = $this->bulkNotificationRepository->find($message->getBulkNotificationId());

        if (!$bulkNotification) {
            $this->logger->error('BulkNotificationHandler: bulkNotification not found for ID: ' . $message->getBulkNotificationId());
            return;
        }

        $event = $bulkNotification->getSubscription()->getEvent();

        if($event) {
            $tickets = $this->ticketRepository->findNonCancelledByEvent($event);
            foreach ($tickets as $ticket) {
                $this->createUserNotification($ticket->getOwner(), $bulkNotification);
            }
            $this->logger->info('BulkNotificationHandler: Created userNotification entities for active event attendees, BulkNotification ID: ' . $bulkNotification->getId());
        }
        else {
            $userSubscriptions = $bulkNotification->getSubscription()->getUserSubscriptions();
            foreach ($userSubscriptions as $userSubscription) {
                $this->createUserNotification($userSubscription->getOwner(), $bulkNotification);
            }
            $this->logger->info('BulkNotificationHandler: Created userNotification entities for bulkNotification ID: ' . $bulkNotification->getId());
        }
    }

    private function createUserNotification($user, $bulkNotification): void
    {
        // Check if the UserNotification already exists
        $existingEntry = $this->userNotificationRepository->findOneBy([
            'user' => $user,
            'bulkNotification' => $bulkNotification
        ]);

        if ($existingEntry) {
            $this->logger->info('UserNotification entry already exists for user ID: ' . $user->getId() . ' and BulkNotification ID: ' . $bulkNotification->getId());
            return;
        }

        // Create and save the UserNotification
        $userNotification = new UserNotification();
        $userNotification->setUser($user);
        $userNotification->setBulkNotification($bulkNotification);
        $userNotification->setData($bulkNotification->getData());
        $userNotification->setTemplateId($bulkNotification->getTemplateId());
        $userNotification->setSent(false);

        $this->userNotificationRepository->save($userNotification);
    }

}
