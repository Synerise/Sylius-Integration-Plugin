<?php

namespace Synerise\SyliusIntegrationPlugin\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ChannelInterface;

class SynchronizationRepository extends EntityRepository
{
    public function countByChannelWithFilters(
        ChannelInterface $channel,
        array $criteria = []
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

}
