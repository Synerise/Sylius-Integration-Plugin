<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler;

use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\Sdk\Api\Config;
use Synerise\SyliusIntegrationPlugin\Api\ClientBuilderFactory;

interface RequestHandlerInterface
{
    public function __construct(ClientBuilderFactory $clientBuilderFactory);

    /**
     * @return Promise<mixed>
     * @throws \Exception
     */
    public function send(
        Parsable $payload,
        Config $config, string|int|null $channelId,
        array $additionalData
    ): Promise;

    /**
     * @return array{class-string<Parsable>,string} $type The type for the Parsable object.
     */
    public function getType(): array;
}
