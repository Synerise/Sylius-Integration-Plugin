<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler;

use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\Sdk\Api\ClientBuilder;
use Synerise\Sdk\Api\Config;
use Synerise\Sdk\Api\ClientBuilderFactoryInterface;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationInterface;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationInterface;

abstract class AbstractRequestHandler implements RequestHandlerInterface
{
    /** @var class-string<Parsable> */
    public static string $requestClass;

    public static string $createMethod = 'createFromDiscriminatorValue';

    public function __construct(
        private ClientBuilderFactoryInterface $clientBuilderFactory,
        private ChannelConfigurationFactory $channelConfigurationFactory,
        private SynchronizationConfigurationFactory $synchronizationConfigurationFactory,
    ) {
    }

    /**
     * @inheritDoc
     */
    abstract public function send(Parsable $payload, string|int $channelId): Promise;

    /**
     * @inheritDoc
     */
    public function getType(): array
    {
        return [
            static::$requestClass,
            static::$createMethod,
        ];
    }

    protected function getClientBuilder(Config $config): ClientBuilder
    {
        /** @var ClientBuilder $clientBuilder */
        $clientBuilder = $this->clientBuilderFactory->create($config);

        return $clientBuilder;
    }

    protected function getChannelConfiguration(string|int|null $channelId): ?ChannelConfigurationInterface
    {
        return $this->channelConfigurationFactory->get($channelId);
    }

    protected function getSynchronizationConfiguration(string|int|null $channelId): ?SynchronizationConfigurationInterface
    {
        return $this->synchronizationConfigurationFactory->get($channelId);
    }
}
