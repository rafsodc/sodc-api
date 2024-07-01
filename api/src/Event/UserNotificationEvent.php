<?php

namespace App\Event;

use App\Entity\UserNotification;
use Symfony\Contracts\EventDispatcher\Event;

class UserNotificationEvent extends Event
{
    private $userNotification;

    public function __construct(UserNotification $userNotification)
    {
        $this->userNotification = $userNotification;
    }

    public function getUserNotification(): UserNotification
    {
        return $this->userNotification;
    }
}
