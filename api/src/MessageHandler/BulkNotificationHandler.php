<?php

namespace App\MessageHandler;

//use App\Entity\bulkNotification;
use App\Entity\UserNotification;
use App\Entity\User;
use App\Message\BulkNotificationMessage;
use App\Message\UserNotificationMessage;
use App\Repository\BulkNotificationRepository;
use App\Repository\UserNotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;

class BulkNotificationHandler implements MessageHandlerInterface
{
    private $bulkNotificationRepository;
    private $userNotificationRepository;
    private $entityManager;
    private $messageBus;
    private $logger;

    public function __construct(
        BulkNotificationRepository $bulkNotificationRepository,
        UserNotificationRepository $userNotificationRepository,
        EntityManagerInterface $entityManager,
        MessageBusInterface $messageBus,
        LoggerInterface $logger
    ) {
        $this->bulkNotificationRepository = $bulkNotificationRepository;
        $this->userNotificationRepository = $userNotificationRepository;
        $this->entityManager = $entityManager;
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

        $roles = $bulkNotification->getRoles();
        $dataTemplate = $bulkNotification->getData();
        $batchSize = $message->getBatchSize();
        $batchOffset = $message->getBatchOffset();

        $users = $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            // ->where(
            //     $qb->expr()->notIn(
            //         'u.id',
            //         $this->entityManager->getRepository(UserNotification::class)->createQueryBuilder('un')
            //             ->select('IDENTITY(un.user)')
            //             ->where('un.bulkNotification = :bulkNotification')
            //             ->getDQL()
            //     )
            // )
            // ->setParameter('bulkNotification', $bulkNotification)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults($batchSize)
            ->setFirstResult($batchOffset)
            ->getQuery()
            ->getResult();

        foreach ($users as $user) {
            if ($user->hasAnyRoleWithExclusions($roles)) {
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

                //$this->logger->info($userNotification->getId());
                // Dispatch the UserNotification for further processing asynchronously
                $this->messageBus->dispatch(new UserNotificationMessage($userNotification->getId()));
            }
        }

        $this->logger->info('BulkNotificationHandler: Created userNotification entities for bulkNotification ID: ' . $bulkNotification->getId() . ' with batch offset: ' . $batchOffset);

        // Check if there are more users to process
        if (count($users) === $batchSize) {
            $this->messageBus->dispatch(new BulkNotification($message->getbulkNotificationId(), $batchSize, $batchOffset + $batchSize));
        }
    }

}
