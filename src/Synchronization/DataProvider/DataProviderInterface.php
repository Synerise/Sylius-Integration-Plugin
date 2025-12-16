<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Synchronization\DataProvider;

use DateTimeImmutable;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Resource\Model\ResourceInterface;

interface DataProviderInterface
{
    public function getIds(ChannelInterface $channel, ?\DateTimeImmutable $sinceWhen, ?\DateTimeImmutable $untilWhen): iterable;

    public function getEntity(int $id): ?ResourceInterface;
}
