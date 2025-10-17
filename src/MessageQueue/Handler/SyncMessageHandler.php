<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\MessageQueue\Handler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Synerise\SyliusIntegrationPlugin\Entity\Synchronization;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationInterface;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationStatus;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\SyncMessage;
use Synerise\SyliusIntegrationPlugin\Repository\SynchronizationRepositoryInterface;
use Synerise\SyliusIntegrationPlugin\Synchronization\SynchronizationProcessorFactory;
use Webmozart\Assert\Assert;

#[AsMessageHandler(bus: 'synerise.synchronization_bus')]
class SyncMessageHandler
{
    /**
     * @param SynchronizationRepositoryInterface<SynchronizationInterface> $synchronizationRepository
     */
    public function __construct(
        private SynchronizationRepositoryInterface $synchronizationRepository,
        private SynchronizationProcessorFactory $synchronizationProcessorFactory,
    ) {
    }

    public function __invoke(SyncMessage $syncMessage): void
    {
        /** @var Synchronization|null $synchronization */
        $synchronization = $this->synchronizationRepository->find($syncMessage->getSynchronizationId());
        if (!$type = $synchronization?->getType()) {
            return;
        }

        if ($synchronization->getStatus() === SynchronizationStatus::Cancelled) {
            return;
        }

        $processor = $this->synchronizationProcessorFactory->get($type->value);
        Assert::notNull($processor);

        $processor->processSynchronization($syncMessage);
    }
}
