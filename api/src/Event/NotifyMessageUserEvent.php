<?php

namespace App\Event;

use App\Entity\NotifyMessageUser;
use Symfony\Contracts\EventDispatcher\Event;

class NotifyMessageUserEvent extends Event
{
    private $notifyMessageUser;

    public function __construct(NotifyMessageUser $notifyMessageUser)
    {
        $this->notifyMessageUser = $notifyMessageUser;
    }

    public function getNotifyMessageUser(): NotifyMessageUser
    {
        return $this->notifyMessageUser;
    }
}
