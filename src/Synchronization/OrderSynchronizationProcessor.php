<?php

namespace Synerise\SyliusIntegrationPlugin\Synchronization;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\Order;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Synerise\Sdk\Api\Config;
use Synerise\SyliusIntegrationPlugin\Api\ClientBuilderFactory;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Entity\Synchronization;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\SyncMessage;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\SyncStartMessage;
use Synerise\SyliusIntegrationPlugin\Processor\OrderResourceProcessor;
use Webmozart\Assert\Assert;

class OrderSynchronizationProcessor extends OrderResourceProcessor implements SynchronizationProcessorInterface
{
    public function __construct(
        private EntityManagerInterface      $entityManager,
        private MessageBusInterface         $messageBus,
        private ChannelConfigurationFactory $channelConfigurationFactory,
        private ClientBuilderFactory        $clientBuilderFactory,
    )
    {
    }

    /**
     * @throws ExceptionInterface
     */
    public function dispatchSynchronization(SyncStartMessage $message): void
    {
        $synchronization = $this->entityManager->getRepository(Synchronization::class)->find($message->getSynchronizationId());
        if (null === $synchronization?->getId()) {
            return;
        }

        $orderIds = [];
        $totalCount = 0;

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select('o.id')
            ->from(Order::class, 'o')
            ->where('o.channel = :channel')
            ->setParameter('channel', $synchronization->getChannel());
        $iterableResult = $queryBuilder->getQuery()->toIterable();

        foreach ($iterableResult as $row) {
            $totalCount += 1;

            Assert::isArray($row);
            Assert::keyExists($row, 'id');
            $orderIds[] = $row['id'];

            if (count($orderIds) >= 20) {
                $syncMessage = new SyncMessage($synchronization->getId(), $orderIds);
                $this->messageBus->dispatch($syncMessage);
                $orderIds = [];
            }
        }

        if (!empty($orderIds)) {
            $syncMessage = new SyncMessage($synchronization->getId(), $orderIds);
            $this->messageBus->dispatch($syncMessage);
        }

        $synchronization->setTotal($totalCount);
        $this->entityManager->persist($synchronization);
    }

    public function processSynchronization(SyncMessage $message): void
    {
        $orderRepository = $this->entityManager->getRepository(Order::class);
        $synchronizationRepository = $this->entityManager->getRepository(Synchronization::class);

        $synchronization = $synchronizationRepository->find($message->getSynchronizationId());
        if (null === $synchronization) {
            return;
        }

        $ordersArray = [];
        foreach ($message->getEntityIds() as $id) {
            $order = $orderRepository->find($id);
            if ($order === null) {
                continue;
            }

            $ordersArray[] = $this->process($order);
        }

        $channel = $synchronization->getChannel();
        Assert::notNull($channel);

        $this->sendToSynerise($channel, $ordersArray);
        $synchronization->setSent($synchronization->getSent() + count($message->getEntityIds()));
        $this->entityManager->persist($synchronization);
    }

    private function sendToSynerise(ChannelInterface $channel, array $orders): void
    {
        $channelConfiguration = $this->channelConfigurationFactory->get($channel->getId());
        $config = $channelConfiguration?->getWorkspace();

        Assert::isInstanceOf($config, Config::class);
        $client = $this->clientBuilderFactory->create($config);

        $client->v4()->transactions()->batch()->post($orders)->wait();
    }
}
