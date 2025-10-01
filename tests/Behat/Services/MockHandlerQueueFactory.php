<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Services;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Tests\Synerise\SyliusIntegrationPlugin\Behat\Services\Mock\ResponseFactory\ResponseFactoryInterface;
use Webmozart\Assert\Assert;

class MockHandlerQueueFactory
{
    public const MOCK_HANDLER_QUEUE_COOKIE = 'mock_handler_queue';

    public function __construct(
        private RequestStack $requestStack,
        private array $responseFactories = []
    ) {
    }

    /**
     * @param string[] $keys
     * @return ResponseInterface[]
     */
    public function create(array $keys = []): array
    {
        $queue = [];

        if (empty($keys)) {
            $itemsString = $this->requestStack->getCurrentRequest()->cookies->get(self::MOCK_HANDLER_QUEUE_COOKIE);
            if ($itemsString != null) {
                $keys = json_decode($itemsString);
            }
        }

        foreach ($keys as $key) {
            $queue[] = $this->createResponse($key);
        }

        return $queue;
    }

    public function createResponse(string $key): ResponseInterface
    {
        Assert::keyExists($this->responseFactories, $key);
        Assert::isInstanceOf($this->responseFactories[$key], ResponseFactoryInterface::class);
        return $this->responseFactories[$key]->create();
    }
}
