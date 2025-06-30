<?php

namespace Synerise\SyliusIntegrationPlugin\Synchronization\DataProvider;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Resource\Model\ResourceInterface;

interface DataProviderInterface
{
    public function getIds(ChannelInterface $channel): iterable;

    public function getEntity(int $id): ?ResourceInterface;
}
