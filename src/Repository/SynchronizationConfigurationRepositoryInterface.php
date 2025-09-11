<?php

namespace Synerise\SyliusIntegrationPlugin\Repository;

use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationInterface;

/**
 * @template T of SynchronizationConfigurationInterface
 *
 * @extends RepositoryInterface<T>
 */
interface SynchronizationConfigurationRepositoryInterface extends RepositoryInterface
{
    public function findAllExceptId(?int $id = null): array;

    public function countAll(): int;
}
