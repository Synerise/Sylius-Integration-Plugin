<?php

namespace Synerise\SyliusIntegrationPlugin\Synchronization;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\Customer;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Synerise\Sdk\Api\Config;
use Synerise\SyliusIntegrationPlugin\Api\ClientBuilderFactory;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Entity\Synchronization;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\SyncMessage;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\SyncStartMessage;
use Synerise\SyliusIntegrationPlugin\Processor\CustomerResourceProcessor;
use Webmozart\Assert\Assert;

class CustomerSynchronizationProcessor extends CustomerResourceProcessor implements SynchronizationProcessorInterface
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

        $customerIds = [];
        $totalCount = 0;

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('c.id')->from(Customer::class, 'c');
        $iterableResult = $queryBuilder->getQuery()->toIterable();

        foreach ($iterableResult as $row) {
            $totalCount += 1;

            Assert::isArray($row);
            Assert::keyExists($row, 'id');
            $customerIds[] = $row['id'];

            if (count($customerIds) >= 20) {
                $syncMessage = new SyncMessage($synchronization->getId(), $customerIds);
                $this->messageBus->dispatch($syncMessage);
                $customerIds = [];
            }
        }

        if (!empty($customerIds)) {
            $syncMessage = new SyncMessage($synchronization->getId(), $customerIds);
            $this->messageBus->dispatch($syncMessage);
        }

        $synchronization->setTotal($totalCount);
        $this->entityManager->persist($synchronization);
    }

    public function processSynchronization(SyncMessage $message): void
    {
        $customerRepository = $this->entityManager->getRepository(Customer::class);
        $synchronizationRepository = $this->entityManager->getRepository(Synchronization::class);

        $synchronization = $synchronizationRepository->find($message->getSynchronizationId());
        if (null === $synchronization) {
            return;
        }

        $customersArray = [];
        foreach ($message->getEntityIds() as $id) {
            $customer = $customerRepository->find($id);
            if ($customer === null) {
                continue;
            }

            $customersArray[] = $this->process($customer);
        }

        $channel = $synchronization->getChannel();
        Assert::notNull($channel);

        $this->sendToSynerise($channel, $customersArray);
        $synchronization->setSent($synchronization->getSent() + count($message->getEntityIds()));
        $this->entityManager->persist($synchronization);
    }

    private function sendToSynerise(ChannelInterface $channel, array $customers): void
    {
        $channelConfiguration = $this->channelConfigurationFactory->get($channel->getId());
        $config = $channelConfiguration?->getWorkspace();

        Assert::isInstanceOf($config, Config::class);
        $client = $this->clientBuilderFactory->create($config);

        $client->v4()->clients()->batch()->post($customers)->wait();
    }
}
