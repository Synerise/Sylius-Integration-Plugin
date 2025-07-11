<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\MessageQueue\Handler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\SyncStartMessage;
use Synerise\SyliusIntegrationPlugin\Synchronization\SynchronizationProcessorFactory;

#[AsMessageHandler(bus: 'synerise.synchronization_bus')]
class SyncStartMessageHandler
{
    public function __construct(
        private SynchronizationProcessorFactory $synchronizationProcessorFactory,
    ) {
    }

    public function __invoke(SyncStartMessage $message): void
    {
        $processor = $this->synchronizationProcessorFactory->get($message->getType());

        $processor->dispatchSynchronization($message);
    }
}
