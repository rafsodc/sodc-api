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

class BulkNotificationHandler implements MessageHandlerInterface
{
    private $bulkNotificationRepository;
    private $userNotificationRepository;
    private $messageBus;
    private $logger;

    public function __construct(
        BulkNotificationRepository $bulkNotificationRepository,
        UserNotificationRepository $userNotificationRepository,
        MessageBusInterface $messageBus,
        LoggerInterface $logger
    ) {
        $this->bulkNotificationRepository = $bulkNotificationRepository;
        $this->userNotificationRepository = $userNotificationRepository;
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

        $userSubscriptions = $bulkNotification->getSubscription()->getUserSubscriptions();
        $dataTemplate = $bulkNotification->getData();

        foreach ($userSubscriptions as $userSubscription) {
            $user = $userSubscription->getOwner();
            $existingEntry = $this->userNotificationRepository->findOneBy([
                'user' => $user,
                'bulkNotification' => $bulkNotification
            ]);

            if ($existingEntry) {
                $this->logger->info('UserNotification entry already exists for user ID: ' . $user->getId() . ' and BulkNotification ID: ' . $bulkNotification->getId());
                continue;
            }

            $userNotification = new userNotification();
            $userNotification->setUser($user);
            $userNotification->setbulkNotification($bulkNotification);
            $userNotification->setData($dataTemplate);
            $userNotification->setTemplateId($bulkNotification->getTemplateId());
            $userNotification->setSent(false);

            $this->userNotificationRepository->save($userNotification);

        }

        $this->logger->info('BulkNotificationHandler: Created userNotification entities for bulkNotification ID: ' . $bulkNotification->getId());

    }

}
