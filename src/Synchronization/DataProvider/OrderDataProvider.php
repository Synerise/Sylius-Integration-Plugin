<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Synchronization\DataProvider;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;

class OrderDataProvider implements DataProviderInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function getIds(ChannelInterface $channel): iterable
    {
        $queryBuilder = $this->orderRepository->createQueryBuilder('o');
        $queryBuilder
            ->select('o.id')
            ->where('o.channel = :channel')
            ->andWhere('o.checkoutState = :checkoutState')
            ->setParameters(new ArrayCollection([
                new Parameter('channel', $channel),
                new Parameter('checkoutState', OrderCheckoutStates::STATE_COMPLETED)
            ]));

        return $queryBuilder->getQuery()->toIterable();
    }

    /**
     * @return Order|null
     */
    public function getEntity(int $id): ?ResourceInterface
    {
        return $this->orderRepository->find($id);
    }
}
