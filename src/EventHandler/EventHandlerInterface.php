<?php

namespace Synerise\SyliusIntegrationPlugin\EventHandler;

use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\SyliusIntegrationPlugin\Api\RequestHandlerFactory;

interface EventHandlerInterface
{
    public function processEvent(string $action, Parsable $payload, string|int|null $channelId, array $additionalData): void;
}
