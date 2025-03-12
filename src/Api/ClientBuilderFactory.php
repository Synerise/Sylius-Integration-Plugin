<?php

namespace Synerise\SyliusIntegrationPlugin\Api;

use Synerise\Sdk\Api\ClientBuilder;
use Synerise\Sdk\Api\Config;

class ClientBuilderFactory
{
    public function create(Config $config): ClientBuilder
    {
        return new ClientBuilder($config);
    }
}
