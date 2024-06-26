<?php

namespace App\Message;
use Ramsey\Uuid\UuidInterface;

class UserNotificationMessage
{
    private $userNotificationId;

    public function __construct(UuidInterface $userNotificationId)
    {
        $this->userNotificationId = $userNotificationId;
    }

    public function getUserNotificationId(): UuidInterface
    {
        return $this->userNotificationId;
    }
}
