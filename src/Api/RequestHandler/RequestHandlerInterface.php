<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler;

use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\Sdk\Api\Config;
use Synerise\SyliusIntegrationPlugin\Api\ClientBuilderFactory;

interface RequestHandlerInterface
{
    /**
     * @return Promise<mixed>
     * @throws \Exception
     */
    public function send(Parsable $payload, Config $config, string|int|null $channelId): Promise;

    /**
     * @return array{class-string<Parsable>,string} $type The type for the Parsable object.
     */
    public function getType(): array;
}
