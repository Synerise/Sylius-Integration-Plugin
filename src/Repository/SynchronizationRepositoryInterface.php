<?php

namespace Synerise\SyliusIntegrationPlugin\Repository;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationInterface;

/**
 * @template T of SynchronizationInterface
 *
 * @extends RepositoryInterface<T>
 */
interface SynchronizationRepositoryInterface extends RepositoryInterface
{
    public function countByChannelWithFilters(ChannelInterface $channel, array $criteria = []): int;
}
