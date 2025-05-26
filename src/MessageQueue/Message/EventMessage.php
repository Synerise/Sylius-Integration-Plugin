<?php

namespace Synerise\SyliusIntegrationPlugin\MessageQueue\Message;

use Synerise\Sdk\Api\RequestBody\Events\AbstractCartBuilder;

class EventMessage
{
    private string $payload;

    private string $salesChannelId;

    private string $action;

    private array $additionalData = [];

    public function __construct(string $action, string $payload, string $salesChannelId, array $additionalData)
    {
        $this->action = $action;
        $this->payload = $payload;
        $this->salesChannelId = $salesChannelId;
        $this->additionalData = $additionalData;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    /**
     * @return class-string<AbstractCartBuilder>
     */
    public function getAction(): string
    {
        /* @phpstan-ignore return.type */
        return $this->action;
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }

    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }

}
