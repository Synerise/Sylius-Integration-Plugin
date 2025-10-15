<?php

namespace Synerise\SyliusIntegrationPlugin\Repository;

use Sylius\Component\Core\Model\ChannelInterface;

interface SynchronizationRepositoryInterface
{
    public function countByChannelWithFilters(ChannelInterface $channel, array $criteria = []): int;
}
