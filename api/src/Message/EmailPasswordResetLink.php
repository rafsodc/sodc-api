<?php

namespace App\Message;

class EmailPasswordResetLink
{
  private $passwordTokenId;

  public function __construct(int $passwordTokenId)
  {
    $this->passwordTokenId = $passwordTokenId;
  }

  public function getPasswordTokenId(): int
  {
    return $this->passwordTokenId;
  }

}