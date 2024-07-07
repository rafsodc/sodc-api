<?php

namespace App\Message;
use Ramsey\Uuid\UuidInterface;

class BulkNotificationMessage
{
    private $bulkNotificationId;
    private $batchSize;
    private $batchOffset;

    public function __construct(UuidInterface $bulkNotificationId, int $batchSize = 100, int $batchOffset = 0)
    {
        $this->bulkNotificationId = $bulkNotificationId;
        $this->batchSize = $batchSize;
        $this->batchOffset = $batchOffset;
    }

    public function getBulkNotificationId(): UuidInterface
    {
        return $this->bulkNotificationId;
    }

    public function getBatchSize(): int
    {
        return $this->batchSize;
    }

    public function getBatchOffset(): int
    {
        return $this->batchOffset;
    }
}
