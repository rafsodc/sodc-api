<?php

namespace App\EventSubscriber;

use App\Entity\UserNotification;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Message\UserNotificationMessage as UserNotificationMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ApiPlatform\Core\EventListener\EventPriorities;
use App\Event\UserNotificationEvent;
use Symfony\Component\Messenger\MessageBusInterface;

class UserNotificationSubscriber implements EventSubscriberInterface
{
    private $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserNotificationEvent::class => 'onUserNotificationCreated',
        ];
    }

    public function onUserNotificationCreated(UserNotificationEvent $event): void
    {
        $userNotification = $event->getUserNotification();

        if (!$userNotification instanceof UserNotification) {
            return;
        }

        $this->messageBus->dispatch(new UserNotificationMessage($userNotification->getId()));
    }
}
