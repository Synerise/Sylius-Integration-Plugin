<?php

namespace Synerise\SyliusIntegrationPlugin\EventHandler;

use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\Sdk\Api\Config;
use Synerise\SyliusIntegrationPlugin\Api\RequestHandlerFactory;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Webmozart\Assert\Assert;

class LiveHandler implements EventHandlerInterface
{
    public function __construct(
        private ChannelConfigurationFactory $configurationFactory,
        private RequestHandlerFactory $requestHandlerFactory
    ) {
    }

    public function processEvent(string $action, Parsable $payload, string|int|null $channelId, array $additionalData): void
    {
        $config = $this->configurationFactory->get($channelId)?->getWorkspace();
        Assert::isInstanceOf($config, Config::class);

        $this->requestHandlerFactory->get($action)->send($payload, $config, $channelId, $additionalData)->wait();
    }
}
