<?php
namespace App\MessageHandler;
use App\Message\ContactSubmit;
use App\Repository\ContactRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Service\NotifyClient;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ContactSubmitHandler implements MessageHandlerInterface
{
  private $notifyClient;
  private $template;
  private $contactRepository;
  private $params;

  public function __construct(NotifyClient $notifyClient, ContactRepository $contactRepository, ParameterBagInterface $params)
  {
    $this->notifyClient = $notifyClient->client;
    $this->template = $notifyClient->templates['contact_submit'];
    $this->contactRepository = $contactRepository;
    $this->params = $params;
  }

  public function __invoke(ContactSubmit $contactSubmit)
  {
      $contactId = $contactSubmit->getContactId();
      $contact = $this->contactRepository->find($contactId);
      $email = $this->params->get('app.contact.email');

      $this->notifyClient->sendEmail(
        $email,
        $this->template,
        [
          'name' => $contact->getName(),
          'email' => $contact->getEmail(),
          'subject' => $contact->getSubject(),
          'content' => $contact->getMessage(),
          'date' => $contact->getCreatedDate()->format('D j M y - H:i:s e'),
        ],
      );
  }
}