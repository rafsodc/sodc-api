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

  public function __construct(ClientInterface $clientInterface, $key, $templates)
  {
    $this->templates = $templates;
    $this->client = new Client([
      'httpClient'    => $clientInterface,
      'apiKey'        => $key
    ]);
  }
}