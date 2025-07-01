<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Synchronization\DataProvider;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Resource\Model\ResourceInterface;

class CustomerDataProvider implements DataProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function getIds(ChannelInterface $channel): iterable
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('c.id')->from(Customer::class, 'c');

        return $queryBuilder->getQuery()->toIterable();
    }

    /**
     * @return Customer|null
     */
    public function getEntity(int $id): ?ResourceInterface
    {
        return $this->entityManager->getRepository(Customer::class)->find($id);
    }
}
