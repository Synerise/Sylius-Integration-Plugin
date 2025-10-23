<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationInterface;

/**
 * @template T of SynchronizationInterface
 *
 * @implements SynchronizationRepositoryInterface<T>
 */
class SynchronizationRepository extends EntityRepository implements SynchronizationRepositoryInterface
{
    public function countByChannelWithFilters(
        ChannelInterface $channel,
        array $criteria = [],
    ): int {
        $qb = $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->andWhere('s.channel = :channel')
            ->setParameter('channel', $channel);

        if (isset($criteria['status'])) {
            $qb->andWhere('s.status = :status')
                ->setParameter('status', $criteria['status']);
        }

        if (isset($criteria['type'])) {
            $qb->andWhere('s.type = :type')
                ->setParameter('type', $criteria['type']);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function incrementSent(int $id, int $by): void
    {
        if ($by <= 0) {
            throw new \InvalidArgumentException('Increment value must be positive.');
        }

        $this->createQueryBuilder('s')
            ->update()
            ->set('s.sent', 's.sent + :by')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->setParameter('by', $by)
            ->getQuery()
            ->execute();
    }

}
