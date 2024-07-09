<?php

namespace App\Message;
use Ramsey\Uuid\UuidInterface;

class BulkNotificationMessage
{
    private $bulkNotificationId;

    public function __construct(UuidInterface $bulkNotificationId)
    {
        $this->bulkNotificationId = $bulkNotificationId;
    }

    public function getBulkNotificationId(): UuidInterface
    {
        return $this->bulkNotificationId;
    }
}
