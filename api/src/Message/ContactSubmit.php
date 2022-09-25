<?php

namespace App\Message;

class ContactSubmit
{
  private $contactId;

  public function __construct(int $contactId)
  {
    $this->contactId = $contactId;
  }

  public function getContactId(): int
  {
    return $this->contactId;
  }

}