<?php 

namespace App\EventSubscriber;

use CoopTilleuls\ForgotPasswordBundle\Event\CreateTokenEvent;
use CoopTilleuls\ForgotPasswordBundle\Event\UpdatePasswordEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Service\NotifyClient;
use App\Message\ContactSubmit;
use Symfony\Component\Messenger\MessageBusInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\DataPersister\UserDataPersister;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Contact;

final class ContactEventSubscriber implements EventSubscriberInterface
{
    private $messageBus;
    private $userManager;

    public function __construct(MessageBusInterface $messageBus, UserDataPersister $userManager)
    {
        $this->messageBus = $messageBus;
        $this->userManager = $userManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['onContactPost', EventPriorities::POST_WRITE],
        ];
    }

    public function onContactPost(ViewEvent $event)
    {
        $contact = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$contact instanceof Contact || Request::METHOD_POST !== $method) {
            return;
        }

        $message = new ContactSubmit($contact->getId());
        $this->messageBus->dispatch($message);
    }
}