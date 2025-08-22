<?php

namespace Synerise\SyliusIntegrationPlugin\Repository;


use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;

/**
 * @template T of ChannelConfigurationInterface
 *
 * @extends RepositoryInterface<T>
 */
interface ChannelConfigurationRepositoryInterface extends RepositoryInterface
{
    /**
     * @phpstan-return list<T> The entities.
     */
    public function findAllExceptId(?int $id = null);

    public function countAll(): int;
}
