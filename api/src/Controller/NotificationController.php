<?php

namespace App\Controller;

use App\Entity\BulkNotification;
use App\Message\UserNotificationMessage;
use App\Repository\BulkNotificationRepository;
use App\Repository\UserNotificationRepository;
use App\Repository\NotificationReturnRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

class NotificationController extends AbstractController
{
    private $bulkNotificationRepository;
    private $userNotificationRepository;
    private $notificationReturnRepository;
    private $entityManager;
    private $messageBus;
    private $logger;
    private $notificationCallbackToken;

    public function __construct(
        BulkNotificationRepository $bulkNotificationRepository,
        UserNotificationRepository $userNotificationRepository,
        NotificationReturnRepository $notificationReturnRepository,
        EntityManagerInterface $entityManager,
        MessageBusInterface $messageBus,
        LoggerInterface $logger,
        string $notificationCallbackToken
    ) {
        $this->bulkNotificationRepository = $bulkNotificationRepository;
        $this->userNotificationRepository = $userNotificationRepository;
        $this->notificationReturnRepository = $notificationReturnRepository;
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
        $this->logger = $logger;
        $this->notificationCallbackToken = $notificationCallbackToken;
    }

    /**
     * @Route("/bulknotification/{id}/send", methods={"POST"})
     */
    public function sendNotifications($id)
    {
        $bulkNotification = $this->bulkNotificationRepository->find($id);

        if (!$bulkNotification) {
            return new JsonResponse(['error' => 'BulkNotification not found'], 404);
        }

        $this->logger->info('NotificationController: Dispatching UserNotifications for BulkNotification ID: ' . $bulkNotification->getId());

        $userNotifications = $this->userNotificationRepository->findBy(['bulkNotification' => $bulkNotification]);

        if (empty($userNotifications)) {
            return new JsonResponse(['error' => 'No UserNotifications found for this BulkNotification'], 404);
        }

        foreach ($userNotifications as $userNotification) {
            $this->messageBus->dispatch(new UserNotificationMessage($userNotification->getId()));
        }

        $this->logger->info('NotificationController: Dispatched UserNotificationMessages for BulkNotification ID: ' . $bulkNotification->getId());

        return new JsonResponse(['Return' => 'Notifications are being sent'], 200);

    }

    /**
     * @Route("/notification/callback", methods={"POST"})
     */
    public function handleCallback(Request $request): JsonResponse
    {
        $token = $request->headers->get('Authorization');
        $expectedToken = 'Bearer ' . $this->notificationCallbackToken;

        if ($token !== $expectedToken) {
            throw new AccessDeniedException('Invalid token');
        }

        $data = json_decode($request->getContent(), true);

        $notificationReturn = new NotificationReturn();
        $notificationReturn->setNotifyId($data['id']);
        $notificationReturn->setReference($data['reference'] ?? null);
        $notificationReturn->setTo($data['to']);
        $notificationReturn->setReturn($data['Return']);
        $notificationReturn->setCreatedAt(new \DateTime($data['created_at']));
        $notificationReturn->setCompletedAt(isset($data['completed_at']) ? new \DateTime($data['completed_at']) : null);
        $notificationReturn->setSentAt(isset($data['sent_at']) ? new \DateTime($data['sent_at']) : null);
        $notificationReturn->setNotificationType($data['notification_type']);
        $notificationReturn->setTemplateId($data['template_id']);
        $notificationReturn->setTemplateVersion($data['template_version']);

        $userNotification = $this->userNotificationRepository->find($data['reference']);
        if ($userNotification) {
            $notificationReturn->setUserNotification($userNotification);
        }

        $this->entityManager->persist($notificationReturn);
        $this->entityManager->flush();

        return new JsonResponse(['Return' => 'success'], JsonResponse::HTTP_CREATED);
    }
}
