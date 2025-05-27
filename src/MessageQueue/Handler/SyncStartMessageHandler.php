<?php

namespace Synerise\SyliusIntegrationPlugin\MessageQueue\Handler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\SyncStartMessage;
use Synerise\SyliusIntegrationPlugin\Synchronization\SynchronizationFactory;

#[AsMessageHandler(bus: 'synerise.synchronization_bus')]
class SyncStartMessageHandler
{
    public function __construct(
        private SynchronizationFactory $synchronizationFactory,
    )
    {
    }

    public function __invoke(SyncStartMessage $message): void
    {
        $processor = $this->synchronizationFactory->get($message->getType());

        $processor->dispatchSynchronization($message);
    }
}
