<?php
namespace App\MessageHandler;
use App\Message\EmailPasswordResetLink;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Service\NotifyClient;
use App\Repository\PasswordTokenRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EmailPasswordResetLinkHandler implements MessageHandlerInterface
{
  private $notifyClient;
  private $template;
  private $passwordTokenRepository;
  private $params;

  public function __construct(NotifyClient $notifyClient, PasswordTokenRepository $passwordTokenRepository, ParameterBagInterface $params)
  {
    $this->notifyClient = $notifyClient->client;
    $this->template = $notifyClient->templates['password_reset'];
    $this->passwordTokenRepository = $passwordTokenRepository;
    $this->params = $params;
  }

  public function __invoke(EmailPasswordResetLink $emailPasswordResetLink)
  {
      $passwordTokenId = $emailPasswordResetLink->getPasswordTokenId();
      $passwordToken = $this->passwordTokenRepository->find($passwordTokenId);
      
      $email = $passwordToken->getUser()->getEmail();
      $firstname = $passwordToken->getUser()->getFirstName();
      $server = $this->params->get('server_name');
      $link = sprintf('https://%s/forgot-password/%s', $server, $passwordToken->getToken());

      $this->notifyClient->sendEmail(
        $email,
        $this->template,
        [
          'firstname' => $firstname,
          'link' => $link
        ],
      );
  }
}