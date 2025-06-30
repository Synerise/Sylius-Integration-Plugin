<?php

namespace Synerise\SyliusIntegrationPlugin\MessageQueue\Message;

class SyncStartMessage
{
    private int $synchronizationId;
    private string $type;

    public function __construct(int $synchronizationId, string $type){
        $this->synchronizationId = $synchronizationId;
        $this->type = $type;
    }

    public function getSynchronizationId(): int
    {
        return $this->synchronizationId;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
