<?php

namespace Synerise\SyliusIntegrationPlugin\MessageQueue\Handler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Synerise\SyliusIntegrationPlugin\Entity\Synchronization;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\SyncMessage;
use Synerise\SyliusIntegrationPlugin\Repository\SynchronizationRepository;
use Synerise\SyliusIntegrationPlugin\Synchronization\SynchronizationProcessorFactory;
use Webmozart\Assert\Assert;

#[AsMessageHandler(bus: 'synerise.synchronization_bus')]
class SyncMessageHandler
{
    public function __construct(
        private SynchronizationRepository $synchronizationRepository,
        private SynchronizationProcessorFactory $synchronizationProcessorFactory
    )
    {
    }

    public function __invoke(SyncMessage $syncMessage): void
    {
        /** @var Synchronization|null $synchronization */
        $synchronization = $this->synchronizationRepository->find($syncMessage->getSynchronizationId());
        if (!$type = $synchronization?->getType()) {
            return;
        }

        $processor = $this->synchronizationProcessorFactory->get($type->value);
        Assert::notNull($processor);

        $processor->processSynchronization($syncMessage);
    }

}
