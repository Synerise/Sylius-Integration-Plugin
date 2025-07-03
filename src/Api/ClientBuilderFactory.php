<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Api;

use Microsoft\Kiota\Abstractions\RequestAdapter;
use Psr\Log\LoggerInterface;
use Synerise\Sdk\Api\Authentication\AuthenticationProviderFactory;
use Synerise\Sdk\Api\ClientBuilder;
use Synerise\Sdk\Api\Config;
use Synerise\Sdk\Guzzle\RequestAdapterFactory;

class ClientBuilderFactory
{
    public function __construct(
        private LoggerInterface $syneriseLogger,
    ) {
    }

    public function create(Config $config, ?RequestAdapter $requestAdapter = null): ClientBuilder
    {
        $authenticationProviderFactory = new AuthenticationProviderFactory($config);
        if (!$requestAdapter) {
            $requestAdapterFactory = new RequestAdapterFactory($config, $this->syneriseLogger);
            $requestAdapter = $requestAdapterFactory->create(
                $authenticationProviderFactory->create(),
            );
        }

        return new ClientBuilder($config, $requestAdapter);
    }
}
