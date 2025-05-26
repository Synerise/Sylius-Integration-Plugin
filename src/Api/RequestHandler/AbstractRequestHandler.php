<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler;

use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\Sdk\Api\ClientBuilder;
use Synerise\Sdk\Api\Config;
use Synerise\SyliusIntegrationPlugin\Api\ClientBuilderFactory;

abstract class AbstractRequestHandler implements RequestHandlerInterface
{
    /**
     * @var class-string<Parsable>
     */
    public static string $requestClass;

    public static string $createMethod = 'createFromDiscriminatorValue';

    public function  __construct(
        private ClientBuilderFactory $clientBuilderFactory
    ){
    }

    /**
     * @param array $additionalData
     * @inheritDoc
     */
    abstract public function send(Parsable $payload, Config $config, string|int|null $channelId, array $additionalData): Promise;

    /**
     * @inheritDoc
     */
    public function getType(): array
    {
        return [
            static::$requestClass,
            static::$createMethod
        ];
    }

    protected function getClientBuilder(Config $config): ClientBuilder
    {
        return $this->clientBuilderFactory->create($config);
    }
}
