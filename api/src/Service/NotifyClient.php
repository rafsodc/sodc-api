<?php
namespace App\Service;

use Alphagov\Notifications\Client;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Psr\Http\Client\ClientInterface as ClientInterface;

class NotifyClient
{
  public $client;
  public $templates;
  public $replyTos;

  public function __construct(ClientInterface $clientInterface, $key, $templates, $replyTos)
  {
    $this->templates = $templates;
    $this->replyTos = $replyTos;
    $this->client = new Client([
      'httpClient'    => $clientInterface,
      'apiKey'        => $key
    ]);
  }
}