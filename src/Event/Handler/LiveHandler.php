<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event\Handler;

use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\Sdk\Api\Config;
use Synerise\SyliusIntegrationPlugin\Api\EventRequestHandlerFactory;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Webmozart\Assert\Assert;

class LiveHandler implements EventHandlerInterface
{
    public function __construct(
        private ChannelConfigurationFactory $configurationFactory,
        private EventRequestHandlerFactory $requestHandlerFactory,
    ) {
    }

    public function processEvent(string $action, Parsable $payload, string|int|null $channelId): void
    {
        Assert::notNull($channelId);

        $config = $this->configurationFactory->get($channelId)?->getWorkspace();
        Assert::isInstanceOf($config, Config::class);

        $this->requestHandlerFactory->get($action)->send($payload, $channelId)->wait();
    }
}
