<?php

namespace App\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

class BackupQueueStamp implements StampInterface
{
    private $backupQueue;

    public function __construct(bool $backupQueue = true)
    {
        $this->backupQueue = $backupQueue;
    }

    public function getBackupQueue(): bool
    {
        return $this->backupQueue;
    }
}
