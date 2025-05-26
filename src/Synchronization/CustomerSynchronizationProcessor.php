<?php

namespace Synerise\SyliusIntegrationPlugin\Synchronization;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Synerise\SyliusIntegrationPlugin\Entity\Synchronization;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\SyncMessage;
use Synerise\SyliusIntegrationPlugin\MessageQueue\Message\SyncStartMessage;

class CustomerSynchronizationProcessor implements SynchronizationProcessorInterface
{
    public function __construct(
        private EntityManagerInterface    $entityManager,
        private MessageBusInterface       $messageBus
    )
    {
    }

    /**
     * @throws ExceptionInterface
     */
    public function dispatchSynchronization(SyncStartMessage $message): void
    {
        $synchronization = $this->entityManager->getRepository(Synchronization::class)->find($message->getSynchronizationId());
        if (null === $synchronization) {
            return;
        }

        $customerIds = [];
        $totalCount = 0;

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('c.id')->from('Sylius\Component\Core\Model\Customer', 'c');
        $iterableResult = $queryBuilder->getQuery()->toIterable();

        foreach ($iterableResult as $row) {
            $totalCount += 1;
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
        $customerRepository = $this->entityManager->getRepository('Sylius\Component\Core\Model\Customer');
        $synchronizationRepository = $this->entityManager->getRepository(Synchronization::class);

        $synchronization = $synchronizationRepository->find($message->getSynchronizationId());
        if (null === $synchronization) {
            return;
        }


        foreach ($message->getEntityIds() as $id) {
            $customer = $customerRepository->find($id);
            if($customer === null) {
                continue;
            }


        }
    }
}
