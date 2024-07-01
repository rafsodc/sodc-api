<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\BulkNotification;
use App\Event\BulkNotificationEvent;
use App\Message\BulkNotificationMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class BulkNotificationSubscriber implements EventSubscriberInterface
{
    private $messageBus;
    private $entityManager;
    private $logger;

    public function __construct(MessageBusInterface $messageBus, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->messageBus = $messageBus;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['onBulkNotificationPost', EventPriorities::POST_WRITE],
        ];
    }

    public function onBulkNotificationPost(ViewEvent $event)
    {
        $bulkNotification = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$bulkNotification instanceof BulkNotification || Request::METHOD_POST !== $method) {
            return;
        }

        // Log the creation of the bulk notification
        $this->logger->info('BulkNotificationSubscriber: BulkNotification created with ID: ' . $bulkNotification->getId());

        // Dispatch the BulkNotificationMessage to start the asynchronous processing
        $this->messageBus->dispatch(new BulkNotificationMessage($bulkNotification->getId()));
    }
}
