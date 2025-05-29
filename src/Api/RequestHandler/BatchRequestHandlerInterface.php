<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler;

use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Sylius\Component\Channel\Model\ChannelInterface;

interface BatchRequestHandlerInterface
{
    /**
     * @param Parsable[] $payload
     * @return Promise<mixed>
     * @throws \Exception
     */
    public function send(array $payload, ChannelInterface $channel): Promise;
}
