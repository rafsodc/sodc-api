<?php

namespace App\Middleware;

use Psr\Log\LoggerInterface;
use App\Stamp\BackupQueueStamp;
use App\Message\BackupQueueWrapper;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class TransportExceptionMiddleware implements MiddlewareInterface
{
    private $backupTransport;
    private $logger;

    public function __construct(MessageBusInterface $messageBus, LoggerInterface $logger)
    {
        $this->messageBus = $messageBus;
        $this->logger = $logger;
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        try {
            return $stack->next()->handle($envelope, $stack);
        } catch (TransportException $e) {

            if (null === $envelope->last(BackupQueueStamp::class)) {
                $envelope = $envelope->with(new BackupQueueStamp());
                $this->logger->info("First error!");
                
                $this->logger->critical('Failed to send message to the AMQP server.', [
                    'exception' => $e,
                    'message' => $envelope->getMessage(),
                ]);
            
                $wrapperMessage = new BackupQueueWrapper($envelope);
                $this->messageBus->dispatch($wrapperMessage);
            }

            $this->logger->info("Subsequent error!");
            
            throw $e; // Re-throwing the exception to allow other middlewares to handle it, if needed
        }
    }
}