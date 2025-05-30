<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Resource;

use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Resource\Model\ResourceInterface;

interface RequestMapperInterface
{
    public function prepare(
        ResourceInterface $resource,
        string $type = 'live',
        ?ChannelInterface $channel = null
    ): Parsable;
}
