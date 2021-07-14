<?php 

namespace App\EventSubscriber;

use CoopTilleuls\ForgotPasswordBundle\Event\CreateTokenEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Service\NotificationClient;

final class ForgotPasswordEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Alphagov\Notifications\Client $client
     */
    private $client;

    private $template;

    public function __construct(NotificationClient $notificationClient)
    {
        $this->template = $notificationClient->templates['password_reset'];
        $this->client = $notificationClient->client;
    }

    public static function getSubscribedEvents()
    {
        return [
            CreateTokenEvent::class => 'onCreateToken',
        ];
    }

    public function onCreateToken(CreateTokenEvent $event)
    {
        $passwordToken = $event->getPasswordToken();
        $user = $passwordToken->getUser();

        $this->client->sendEmail(
            $user->getEmail(),
            $this->template
        );

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