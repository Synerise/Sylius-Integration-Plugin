<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Services\Mock\ResponseFactory;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class CreateCatalogSuccess implements ResponseFactoryInterface
{
    public function create(): ResponseInterface
    {
        return new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'data' => [
                'id' => 1234,
                "businessProfileId" => 1000,
                'name' => 'channel-1',
                'lastModified' => '2025-01-01T00:00:00.000Z',
                'creationDate' => '2025-01-01T00:00:00.000Z',
                'beforeFiltering' => false,
                'fields' => [],
                'primaryKey' => null,
                'createdBy' => -1,
                'modifiedBy' => -1
            ],
            'metaData' => [
                'totalCount' => 1,
                'requestTime' => '0.01 [s]'
            ],
        ]));
    }
}
