<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Services\Mock\ResponseFactory;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class GetCatalogsSuccess implements ResponseFactoryInterface
{
    public function create(): ResponseInterface
    {
        return new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'data' => [
                [
                    'id' => 367753,
                    'name' => 'channel-1',
                    'author' => 'Unknown',
                    'lastModified' => '2025-01-01T00:00:00.000Z',
                    'creationDate' => '2025-01-01T00:00:00.000Z'
                ],
            ],
            'metaData' => [
                'totalCount' => 1,
                'requestTime' => '0.01 [s]'
            ],
        ]));
    }
}
