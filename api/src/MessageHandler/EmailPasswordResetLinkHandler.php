<?php
namespace App\MessageHandler;
use App\Message\EmailPasswordResetLink;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Service\NotifyClient;
use App\Repository\PasswordTokenRepository;

class EmailPasswordResetLinkHandler implements MessageHandlerInterface
{
  private $notifyClient;
  private $template;
  private $passwordTokenRepository;

  public function __construct(NotifyClient $notifyClient, PasswordTokenRepository $passwordTokenRepository)
  {
    $this->notifyClient = $notifyClient->client;
    $this->template = $notifyClient->templates['password_reset'];
    $this->passwordTokenRepository = $passwordTokenRepository;
  }

  public function __invoke(EmailPasswordResetLink $emailPasswordResetLink)
  {
      $passwordTokenId = $emailPasswordResetLink->getPasswordTokenId();
      $passwordToken = $this->passwordTokenRepository->find($passwordTokenId);
      
      $email = $passwordToken->getUser()->getEmail();
      $firstname = $passwordToken->getUser()->getUsername();
      $link = sprintf('https://test1.jackdipper.com/forgot-password/%s', $passwordToken->getToken());

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