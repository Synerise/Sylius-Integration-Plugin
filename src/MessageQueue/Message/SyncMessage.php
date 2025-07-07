<?php

declare(strict_types=1);

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

    public function getSynchronizationId(): int
    {
        return $this->synchronizationId;
    }

    public function getEntityIds(): array
    {
        return $this->entityIds;
    }
}
