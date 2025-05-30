<?php

namespace Synerise\SyliusIntegrationPlugin\Event\Handler;

use Microsoft\Kiota\Abstractions\Serialization\Parsable;

interface EventHandlerInterface
{
    public function processEvent(string $action, Parsable $payload, string|int|null $channelId): void;
}
