<?php

namespace App\Message;
use Ramsey\Uuid\UuidInterface;

class NotifyMessageUser
{
    private $notifyMessageUserId;

    public function __construct(UuidInterface $notifyMessageUserId)
    {
        $this->notifyMessageUserId = $notifyMessageUserId;
    }

    public function getNotifyMessageUserId(): UuidInterface
    {
        return $this->notifyMessageUserId;
    }
}
