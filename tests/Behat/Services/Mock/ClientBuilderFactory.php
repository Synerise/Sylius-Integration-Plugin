<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Services\Mock;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Microsoft\Kiota\Abstractions\Authentication\AnonymousAuthenticationProvider;
use Microsoft\Kiota\Abstractions\RequestAdapter;
use Microsoft\Kiota\Http\GuzzleRequestAdapter;
use Microsoft\Kiota\Serialization\Json\JsonParseNodeFactory;
use Microsoft\Kiota\Serialization\Json\JsonSerializationWriterFactory;
use Synerise\Sdk\Api\Authentication\AuthenticationProviderFactory;
use Synerise\Sdk\Api\ClientBuilder;
use Synerise\Sdk\Api\ClientBuilderFactory as BaseClientBuilderFactory;
use Synerise\Sdk\Api\Config;
use Synerise\Sdk\Guzzle\RequestAdapterFactory;
use Tests\Synerise\SyliusIntegrationPlugin\Behat\Services\MockHandlerQueueFactory;

class ClientBuilderFactory extends BaseClientBuilderFactory
{
    public function __construct(
        private MockHandlerQueueFactory $mockHandlerQueueFactory,
        AuthenticationProviderFactory $authenticationProviderFactory,
        RequestAdapterFactory $requestAdapterFactory,
    ) {
        parent::__construct($authenticationProviderFactory, $requestAdapterFactory);
    }

    public function create(?Config $config, ?RequestAdapter $requestAdapter = null): ?ClientBuilder
    {
        $queue = $this->mockHandlerQueueFactory->create();
        if (!empty($queue)) {
            $requestAdapter = new GuzzleRequestAdapter(
                new AnonymousAuthenticationProvider(),
                new JsonParseNodeFactory(),
                new JsonSerializationWriterFactory(),
                new Client(['handler' => HandlerStack::create(new MockHandler($queue))])
            );

            return new ClientBuilder($config, $requestAdapter);
        } else {
            return parent::create($config, $requestAdapter);
        }
    }
}
