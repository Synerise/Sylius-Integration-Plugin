<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Services\Mock\ResponseFactory;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class GetCatalogsSuccess implements ResponseFactoryInterface
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public function create(): ResponseInterface
    {
        $channelId = $this->requestStack->getCurrentRequest()?->cookies->get('channelId') ?? -1;
        return new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'data' => [
                [
                    'id' => 123,
                    'name' => 'channel-'.$channelId,
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
