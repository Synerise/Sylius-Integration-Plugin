<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Repository;

use Doctrine\ORM\EntityRepository as BaseEntityRepository;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;

/**
 * @template T of ChannelConfigurationInterface
 *
 * @implements ChannelConfigurationRepositoryInterface<T>
 */
class ChannelConfigurationRepository extends EntityRepository implements ChannelConfigurationRepositoryInterface
{
    /**
     * @return list<T> The entities.
     */
    public function findAllExceptId(?int $id = null)
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
