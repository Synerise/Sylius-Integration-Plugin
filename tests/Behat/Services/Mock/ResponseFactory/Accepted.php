<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Services\Mock\ResponseFactory;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class Accepted implements ResponseFactoryInterface
{
    public function create(): ResponseInterface
    {
        return new Response(202, ['Content-Type' => 'application/json']);
    }
}
