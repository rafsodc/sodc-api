<?php 

namespace App\EventSubscriber;

use CoopTilleuls\ForgotPasswordBundle\Event\CreateTokenEvent;
use CoopTilleuls\ForgotPasswordBundle\Event\UpdatePasswordEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Service\NotifyClient;
use App\Message\EmailPasswordResetLink;
use Symfony\Component\Messenger\MessageBusInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\DataPersister\UserDataPersister;

final class ForgotPasswordEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Alphagov\Notifications\Client $client
     */
    private $client;
    private $template;
    private $messageBus;
    private $userManager;

    public function __construct(NotifyClient $notifyClient, MessageBusInterface $messageBus, UserDataPersister $userManager)
    {
        $this->template = $notifyClient->templates['password_reset'];
        $this->client = $notifyClient->client;
        $this->messageBus = $messageBus;
        $this->userManager = $userManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            CreateTokenEvent::class => 'onCreateToken',
            UpdatePasswordEvent::class => 'onUpdatePassword',
        ];
    }

    public function onCreateToken(CreateTokenEvent $event)
    {
        $message = new EmailPasswordResetLink($event->getPasswordToken()->getId());
        $this->messageBus->dispatch($message);
    }

    public function onUpdatePassword(UpdatePasswordEvent $event)
    {
        $passwordToken = $event->getPasswordToken();
        $user = $passwordToken->getUser();
        $user->setPlainPassword($event->getPassword());
        $this->userManager->persist($user);
    }
}