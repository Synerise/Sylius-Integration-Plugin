<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Services\Mock\ResponseFactory;

use Psr\Http\Message\ResponseInterface;

interface ResponseFactoryInterface
{
    public function create(): ResponseInterface;
}
