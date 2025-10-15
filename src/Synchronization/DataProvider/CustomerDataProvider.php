<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Synchronization\DataProvider;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;

class CustomerDataProvider implements DataProviderInterface
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
    ) {
    }

    public function getIds(ChannelInterface $channel): iterable
    {
        $queryBuilder = $this->customerRepository->createQueryBuilder('c');
        $queryBuilder->select('c.id');

        return $queryBuilder->getQuery()->toIterable();
    }

    /**
     * @return Customer|null
     */
    public function getEntity(int $id): ?ResourceInterface
    {
        return $this->customerRepository->find($id);
    }
}
