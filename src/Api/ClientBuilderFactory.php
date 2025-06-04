<?php

namespace Synerise\SyliusIntegrationPlugin\Api;

use Loguzz\Middleware\LogMiddleware;
use Microsoft\Kiota\Abstractions\RequestAdapter;
use Psr\Log\LoggerInterface;
use Synerise\Sdk\Api\Authentication\AuthenticationProviderFactory;
use Synerise\Sdk\Api\ClientBuilder;
use Synerise\Sdk\Api\Config;
use Synerise\Sdk\Guzzle\RequestAdapterFactory;
use Synerise\SyliusIntegrationPlugin\Loguzz\Formatter\RequestCurlSanitizedFormatter;

class ClientBuilderFactory
{
    public function __construct(
        private LoggerInterface $syneriseLogger
    ){}

    public function create(Config $config, ?RequestAdapter $requestAdapter = null): ClientBuilder
    {
        $middlewares = [
            new LogMiddleware(
                $this->syneriseLogger,
                ['request_formatter' => new RequestCurlSanitizedFormatter()]
            )
        ];

        $authenticationProviderFactory = new AuthenticationProviderFactory($config);
        if (!$requestAdapter) {
            $requestAdapterFactory = new RequestAdapterFactory($config);
            $requestAdapter = $requestAdapterFactory->create(
                $authenticationProviderFactory->create(),
                $middlewares
            );
        }

        return new ClientBuilder($config, $requestAdapter);
    }
}
