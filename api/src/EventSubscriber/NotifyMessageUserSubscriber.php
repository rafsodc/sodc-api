<?php

namespace App\EventSubscriber;

use App\Entity\NotifyMessageUser;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Message\NotifyMessageUser as NotifyMessageUserMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ApiPlatform\Core\EventListener\EventPriorities;
use App\Event\NotifyMessageUserEvent;
use Symfony\Component\Messenger\MessageBusInterface;

class NotifyMessageUserSubscriber implements EventSubscriberInterface
{
    private $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public static function getSubscribedEvents()
    {
        return [
            NotifyMessageUserEvent::class => 'onNotifyMessageUserCreated',
        ];
    }

    public function onNotifyMessageUserCreated(NotifyMessageUserEvent $event): void
    {
        $notifyMessageUser = $event->getNotifyMessageUser();

        dump($notifyMessageUser);

        if (!$notifyMessageUser instanceof NotifyMessageUser) {
            return;
        }

        $this->messageBus->dispatch(new NotifyMessageUserMessage($notifyMessageUser->getId()));
    }
}
