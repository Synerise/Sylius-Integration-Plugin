<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Services\Mock;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Microsoft\Kiota\Abstractions\Authentication\AnonymousAuthenticationProvider;
use Microsoft\Kiota\Abstractions\Authentication\AuthenticationProvider;
use Microsoft\Kiota\Abstractions\RequestAdapter;
use Microsoft\Kiota\Abstractions\Serialization\ParseNodeFactory;
use Microsoft\Kiota\Abstractions\Serialization\SerializationWriterFactory;
use Microsoft\Kiota\Http\GuzzleRequestAdapter;
use Microsoft\Kiota\Serialization\Json\JsonParseNodeFactory;
use Microsoft\Kiota\Serialization\Json\JsonSerializationWriterFactory;
use Synerise\Sdk\Api\Config;
use Synerise\Sdk\Guzzle\Middleware\LogMiddlewareFactory;
use Synerise\Sdk\Guzzle\RequestAdapterFactoryInterface;

class RequestAdapterFactory implements RequestAdapterFactoryInterface
{
    public function __construct(
        private HandlerQueueFactory  $mockHandlerQueueFactory,
        private LogMiddlewareFactory $logMiddlewareFactory,
    ) {
    }

    public function create(
        Config $config,
        AuthenticationProvider $authenticationProvider,
        array $middlewares = [],
        ?ParseNodeFactory $parseNodeFactory = null,
        ?SerializationWriterFactory $serializationWriterFactory = null
    ): RequestAdapter
    {
        $handler = HandlerStack::create(new MockHandler($this->mockHandlerQueueFactory->create()));
        $handler->push($this->logMiddlewareFactory->create());

        return new GuzzleRequestAdapter(
            new AnonymousAuthenticationProvider(),
            new JsonParseNodeFactory(),
            new JsonSerializationWriterFactory(),
            new Client(['handler' => $handler])
        );
    }
}
