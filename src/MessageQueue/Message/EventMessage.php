<?php

namespace Synerise\SyliusIntegrationPlugin\MessageQueue\Message;

class EventMessage
{
    private string $payload;

    private string $salesChannelId;

    private string $action;

    public function __construct(string $action, string $payload, string $salesChannelId)
    {
        $this->action = $action;
        $this->payload = $payload;
        $this->salesChannelId = $salesChannelId;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    /**
     * @return class-string<\Synerise\Sdk\Api\RequestBody\Events\AbstractCartBuilder>
     */
    public function getAction(): string
    {
        return $this->action;
    }

    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }

}
