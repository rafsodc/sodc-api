<?php

namespace App\EventSubscriber;

use App\Entity\UserNotification;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Message\UserNotificationMessage as UserNotificationMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class UserNotificationSubscriber implements EventSubscriberInterface
{
    private $messageBus;
    private $logger;

    public function __construct(MessageBusInterface $messageBus, LoggerInterface $logger)
    {
        $this->messageBus = $messageBus;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['onUserNotificationCreated', EventPriorities::POST_WRITE],
        ];
    }

    public function onUserNotificationCreated(ViewEvent $event): void
    {
        $userNotification = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        $this->logger->info('UserNotificationSubscriber: Event triggered.');

        if (!$userNotification instanceof UserNotification) {
            $this->logger->info('UserNotificationSubscriber: Not a UserNotification instance or not a POST request.');
            return;
        }

        $this->logger->info('UserNotificationSubscriber: Dispatching UserNotificationMessage for ID: ' . $userNotification->getId());

        $this->messageBus->dispatch(new UserNotificationMessage($userNotification->getId()));
    }
}
