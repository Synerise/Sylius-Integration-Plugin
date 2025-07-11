<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler;

use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;

interface BatchRequestHandlerInterface extends RequestHandlerInterface
{
    /**
     * @param Parsable[] $payload
     *
     * @return Promise<mixed>
     *
     * @throws \Exception
     */
    public function sendBatch(array $payload, int|string $channelId): Promise;
}
