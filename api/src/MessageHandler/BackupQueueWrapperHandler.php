<?php

namespace App\MessageHandler;

use App\Message\BackupQueueWrapper;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class BackupQueueWrapperHandler implements MessageHandlerInterface
{
    private $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(BackupQueueWrapper $message)
    {
        //Unwrap the message to get the original envelope
        $envelope = $message->getOriginalMessage();
        $this->messageBus->dispatch($envelope);
    }
}
