<?php

namespace Synerise\SyliusIntegrationPlugin\MessageQueue\Message;

class SyncMessage
{
    private int $synchronizationId;
    private array $entityIds;

    public function __construct(int $synchronizationId, array $entityIds)
    {
        $this->synchronizationId = $synchronizationId;
        $this->entityIds = $entityIds;
    }

    /**
     * @return int
     */
    public function getSynchronizationId(): int
    {
        return $this->synchronizationId;
    }

    /**
     * @return array
     */
    public function getEntityIds(): array
    {
        return $this->entityIds;
    }
}
