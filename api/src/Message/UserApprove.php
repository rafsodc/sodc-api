<?php

namespace App\Message;

class UserApprove
{
  private $userId;

  public function __construct(int $userId)
  {
    $this->userId = $userId;
  }

  public function getUserId(): int
  {
    return $this->userId;
  }

}