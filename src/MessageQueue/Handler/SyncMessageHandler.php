<?php

namespace Synerise\SyliusIntegrationPlugin\MessageQueue\Handler;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Synerise\SyliusIntegrationPlugin\Entity\Synchronization;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\SyncMessage;
use Synerise\SyliusIntegrationPlugin\Repository\SynchronizationRepository;
use Synerise\SyliusIntegrationPlugin\Synchronization\SynchronizationFactory;

#[AsMessageHandler(bus: 'synerise.synchronization_bus')]
class SyncMessageHandler
{
    public function __construct(
        private SynchronizationRepository $synchronizationRepository,
        private SynchronizationFactory    $synchronizationFactory
    )
    {
    }

    public function __invoke(SyncMessage $syncMessage): void
    {
        /**
         * @var Synchronization $synchronization
         */
        $synchronization = $this->synchronizationRepository->find($syncMessage->getSynchronizationId());
        if ($synchronization === null) {
            return;
        }

        $processor = $this->synchronizationFactory->get($synchronization->getType()->value);


        $processor->processSynchronization($syncMessage);
    }

}
