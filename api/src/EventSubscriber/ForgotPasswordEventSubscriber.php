<?php 

namespace App\EventSubscriber;

use CoopTilleuls\ForgotPasswordBundle\Event\CreateTokenEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Service\NotifyClient;
use App\Message\EmailPasswordResetLink;
use Symfony\Component\Messenger\MessageBusInterface;

final class ForgotPasswordEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Alphagov\Notifications\Client $client
     */
    private $client;

    private $template;

    private $messageBus;

    public function __construct(NotifyClient $notifyClient, MessageBusInterface $messageBus)
    {
        $this->template = $notifyClient->templates['password_reset'];
        $this->client = $notifyClient->client;
        $this->messageBus = $messageBus;
    }

    public static function getSubscribedEvents()
    {
        return [
            CreateTokenEvent::class => 'onCreateToken',
        ];
    }

    public function onCreateToken(CreateTokenEvent $event)
    {
        $message = new EmailPasswordResetLink($event->getPasswordToken()->getId());
        $this->messageBus->dispatch($message);

        //$this->client->sendEmail(
        //    $user->getEmail(),
        //    $this->template
        //);

        /* $message = (new Email())
            ->from('no-reply@example.com')
            ->to($user->getEmail())
            ->subject('Reset your password')
            ->html($this->twig->render(
                'AppBundle:ResetPassword:mail.html.twig',
                [
                    'reset_password_url' => sprintf('http://www.example.com/forgot-password/%s', $passwordToken->getToken()),
                ]
            ));
        if (0 === $this->mailer->send($message)) {
            throw new \RuntimeException('Unable to send email');
        } */
    }
}