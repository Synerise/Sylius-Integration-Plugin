<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Synchronization\DataProvider;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;

class CustomerDataProvider implements DataProviderInterface
{
    /**
     * @param CustomerRepositoryInterface<CustomerInterface> $customerRepository
     */
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
    ) {
    }

    public function getIds(ChannelInterface $channel, DateTimeImmutable $sinceWhen, DateTimeImmutable $untilWhen): iterable
    {
        // @phpstan-ignore-next-line
        $queryBuilder = $this->customerRepository->createQueryBuilder('c');
        $queryBuilder->select('c.id')
            ->where('(c.createdAt BETWEEN :sinceWhen AND :untilWhen) OR (c.updatedAt BETWEEN :sinceWhen AND :untilWhen)')
            ->setParameters(new ArrayCollection([
                new Parameter('sinceWhen', $sinceWhen),
                new Parameter('untilWhen', $untilWhen)
            ]));

        return $queryBuilder->getQuery()->toIterable();
    }

    public function getEntity(int $id): ?ResourceInterface
    {
        return $this->customerRepository->find($id);
    }
}
