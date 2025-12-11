<?php

namespace Tests\Synerise\SyliusIntegrationPlugin\Behat\Services\Mock;

use Psr\Http\Message\ResponseInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Tests\Synerise\SyliusIntegrationPlugin\Behat\Services\Mock\ResponseFactory\ResponseFactoryInterface;
use Webmozart\Assert\Assert;

class HandlerQueueFactory
{
    public const MOCK_HANDLER_QUEUE_COOKIE = 'mock_handler_queue';

    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private RequestStack $requestStack,
        private array $responseFactories = []
    ) {
    }

    public function isE2E()
    {
        return $this->requestStack->getCurrentRequest()?->cookies->get('e2e') || $this->sharedStorage->has('e2e');
    }

    public function create(array $keys = []): array
    {
        $queue = [];

        if (empty($keys)) {
            $keys = $this->getResponseKeys();
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

    private function getResponseKeys(): array
    {
        if ($itemsString = $this->requestStack->getCurrentRequest()?->cookies->get(self::MOCK_HANDLER_QUEUE_COOKIE)) {
            return json_decode($itemsString);
        }

        if ($this->sharedStorage->has(self::MOCK_HANDLER_QUEUE_COOKIE)) {
            return json_decode($this->sharedStorage->get(self::MOCK_HANDLER_QUEUE_COOKIE));
        }

        return [];
    }
}
