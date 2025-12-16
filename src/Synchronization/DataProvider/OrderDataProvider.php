<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Synchronization\DataProvider;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;

class OrderDataProvider implements DataProviderInterface
{
    /**
     * @param OrderRepositoryInterface<OrderInterface> $orderRepository
     */
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function getIds(ChannelInterface $channel, ?\DateTimeImmutable $sinceWhen, ?\DateTimeImmutable $untilWhen): iterable
    {
        // @phpstan-ignore-next-line
        $queryBuilder = $this->orderRepository->createQueryBuilder('o');
        $queryBuilder
            ->select('o.id')
            ->where('o.channel = :channel')
            ->andWhere('o.checkoutState = :checkoutState')
            ->setParameters(new ArrayCollection([
                new Parameter('channel', $channel),
                new Parameter('checkoutState', OrderCheckoutStates::STATE_COMPLETED),
            ]));

        if ($sinceWhen !== null) {
            $queryBuilder
                ->andWhere('o.checkoutCompletedAt >= :sinceWhen')
                ->setParameter('sinceWhen', $sinceWhen);
        }

        if ($untilWhen !== null) {
            $queryBuilder
                ->andWhere('o.checkoutCompletedAt <= :untilWhen')
                ->setParameter('untilWhen', $untilWhen);
        }

        return $queryBuilder->getQuery()->toIterable();
    }

    public function getEntity(int $id): ?ResourceInterface
    {
        return $this->orderRepository->find($id);
    }
}
