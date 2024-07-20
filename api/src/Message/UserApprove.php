<?php

namespace App\Message;
use Ramsey\Uuid\UuidInterface;

class UserApprove
{
  private $userId;

  public function __construct(UuidInterface $userId)
  {
    $this->userId = $userId;
  }

  public function getUserId(): UuidInterface
  {
    return $this->userId;
  }

}