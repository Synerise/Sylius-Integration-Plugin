<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Synchronization;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Synerise\SyliusIntegrationPlugin\Api\RequestHandler\BatchRequestHandlerInterface;
use Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Resource\RequestMapperInterface;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationInterface;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\SyncMessage;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\SyncStartMessage;
use Synerise\SyliusIntegrationPlugin\Repository\SynchronizationRepositoryInterface;
use Synerise\SyliusIntegrationPlugin\Synchronization\DataProvider\DataProviderInterface;
use Webmozart\Assert\Assert;

class SynchronizationProcessor implements SynchronizationProcessorInterface
{
    /**
     * @param SynchronizationRepositoryInterface<SynchronizationInterface> $synchronizationRepository
     */
    public function __construct(
        private DataProviderInterface $dataProvider,
        private RequestMapperInterface $requestMapper,
        private BatchRequestHandlerInterface $requestHandler,
        private SynchronizationRepositoryInterface $synchronizationRepository,
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $messageBus,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function dispatchSynchronization(SyncStartMessage $message): void
    {
        /** @var SynchronizationInterface|null $synchronization */
        $synchronization = $this->synchronizationRepository->find($message->getSynchronizationId());
        if (null === $synchronization?->getId()) {
            return;
        }

        Assert::notNull($synchronization->getChannel());

        $entityIds = [];
        $totalCount = 0;

        foreach ($this->dataProvider->getIds($synchronization->getChannel(), $synchronization->getSinceWhen(), $synchronization->getUntilWhen()) as $row) {
            $totalCount += 1;

            Assert::isArray($row);
            Assert::keyExists($row, 'id');
            $entityIds[] = $row['id'];

            if (count($entityIds) >= 20) {
                $syncMessage = new SyncMessage($synchronization->getId(), $entityIds);
                $this->messageBus->dispatch($syncMessage);
                $entityIds = [];
            }
        }

        if (!empty($entityIds)) {
            $syncMessage = new SyncMessage($synchronization->getId(), $entityIds);
            $this->messageBus->dispatch($syncMessage);
        }

        $synchronization->setTotal($totalCount);
        $this->entityManager->persist($synchronization);
    }

    /**
     * @throws \Exception
     */
    public function processSynchronization(SyncMessage $message): void
    {
        /** @var SynchronizationInterface|null $synchronization */
        $synchronization = $this->synchronizationRepository->find($message->getSynchronizationId());
        if (null === $synchronization?->getId()) {
            return;
        }

        $channel = $synchronization->getChannel();
        Assert::notNull($channel);

        $batch = [];
        foreach ($message->getEntityIds() as $id) {
            $entity = $this->dataProvider->getEntity($id);
            if ($entity !== null) {
                $batch[] = $this->requestMapper->prepare(
                    $entity,
                    'synchronization',
                    $channel,
                );
            }
        }

        $this->requestHandler->sendBatch($batch, $channel->getId());

        $this->synchronizationRepository->incrementSent($synchronization->getId(), count($message->getEntityIds()));
    }
}
