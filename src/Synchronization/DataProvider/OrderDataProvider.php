<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Synchronization\DataProvider;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Resource\Model\ResourceInterface;

class OrderDataProvider implements DataProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function getIds(ChannelInterface $channel): iterable
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select('o.id')
            ->from(Order::class, 'o')
            ->where('o.channel = :channel')
            ->andWhere('o.checkoutState = :checkoutState')
            ->setParameters([
                'channel' => $channel,
                'checkoutState' => OrderCheckoutStates::STATE_COMPLETED,
            ]);

        return $queryBuilder->getQuery()->toIterable();
    }

    /**
     * @return Order|null
     */
    public function getEntity(int $id): ?ResourceInterface
    {
        return $this->entityManager->getRepository(Order::class)->find($id);
    }
}
