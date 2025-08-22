<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationInterface;

/**
 * @template T of SynchronizationConfigurationInterface
 *
 * @implements SynchronizationConfigurationRepositoryInterface<T>
 */
class SynchronizationConfigurationRepository extends EntityRepository implements SynchronizationConfigurationRepositoryInterface
{
    /**
     * @phpstan-return list<T> The entities.
     */
    public function findAllExceptId(?int $id = null): array
    {
        if ($id !== null) {
            /** @var list<T> $result */
            $result = $this->createQueryBuilder('c')
                ->andWhere('c.id != (:id)')
                ->setParameter('id', $id)
                ->getQuery()
                ->getResult();
        } else {
            /** @var list<T> $result */
            $result = $this->findAll();
        }

        return $result;
    }

    public function countAll(): int
    {
        return $this->count([]);
    }
}
