<?php
namespace App\MessageHandler;
use App\Message\UserApprove;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Service\NotifyClient;
use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UserApproveHandler implements MessageHandlerInterface
{
  private $notifyClient;
  private $template;
  private $userRepository;
  private $params;

  public function __construct(NotifyClient $notifyClient, UserRepository $userRepository, ParameterBagInterface $params)
  {
    $this->notifyClient = $notifyClient->client;
    $this->template = $notifyClient->templates['user_approve'];
    $this->userRepository = $userRepository;
    $this->params = $params;
  }

  public function __invoke(UserApprove $userApprove)
  {
      $userId = $userApprove->getUserId();
      $user = $this->userRepository->find($userId);
      
      $email = $user->getEmail();
      $firstname = $user->getFirstName();
      $server = $this->params->get('server_name');
      $link = sprintf('https://%s/members', $server);

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