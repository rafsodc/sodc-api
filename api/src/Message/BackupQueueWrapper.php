<?php

namespace App\Message;

class BackupQueueWrapper
{
    private $originalMessage;

    public function __construct($originalMessage)
    {
        $this->originalMessage = $originalMessage;
    }

    public function getOriginalMessage()
    {
        return $this->originalMessage;
    }
}