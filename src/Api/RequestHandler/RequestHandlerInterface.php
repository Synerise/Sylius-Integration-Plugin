<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler;

use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\Sdk\Api\ClientBuilderFactory;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationFactory;

interface RequestHandlerInterface
{
    public function __construct(
        ClientBuilderFactory $clientBuilderFactory,
        ChannelConfigurationFactory $channelConfigurationFactory,
        SynchronizationConfigurationFactory $synchronizationConfigurationFactory,
    );

    /**
     * @return Promise<mixed>
     *
     * @throws \Exception
     */
    public function send(
        Parsable $payload,
        string|int $channelId,
    ): Promise;

    /**
     * @return array{class-string<Parsable>,string} $type The type for the Parsable object.
     */
    public function getType(): array;
}
