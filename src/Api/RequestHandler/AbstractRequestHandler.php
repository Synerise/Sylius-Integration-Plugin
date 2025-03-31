<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestHandler;

use Http\Promise\Promise;
use Microsoft\Kiota\Abstractions\Serialization\Parsable;
use Synerise\Sdk\Api\ClientBuilder;
use Synerise\Sdk\Api\Config;
use Synerise\SyliusIntegrationPlugin\Api\ClientBuilderFactory;

abstract class AbstractRequestHandler implements RequestHandler
{
    /**
     * @var class-string<Parsable>
     */
    public static string $requestClass;

    public static string $createMethod = 'createFromDiscriminatorValue';

    private ClientBuilderFactory $clientBuilderFactory;

    public function  __construct(
        ClientBuilderFactory $clientBuilderFactory
    ){
        $this->clientBuilderFactory = $clientBuilderFactory;
    }

    /**
     * @inheritDoc
     */
    abstract public function send(Parsable $payload, Config $config): Promise;

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
