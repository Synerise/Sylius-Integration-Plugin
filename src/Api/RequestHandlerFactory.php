<?php

namespace Synerise\SyliusIntegrationPlugin\Api;

use Synerise\SyliusIntegrationPlugin\Api\RequestHandler\RequestHandlerInterface;
use Webmozart\Assert\Assert;

class RequestHandlerFactory
{
    private ClientBuilderFactory $clientBuilderFactory;

    private array $handlersPool;

    private array $handlers;

    public function __construct(ClientBuilderFactory $clientBuilderFactory, array $handlersPool)
    {
        $this->clientBuilderFactory = $clientBuilderFactory;
        $this->handlersPool = $handlersPool;
    }

    public function create(string $action): RequestHandlerInterface
    {
        Assert::keyExists($this->handlersPool, $action);
        Assert::classExists($this->handlersPool[$action]);
        Assert::implementsInterface($this->handlersPool[$action], RequestHandlerInterface::class);

        $this->handlers[$action] = new $this->handlersPool[$action]($this->clientBuilderFactory);

        return $this->handlers[$action];
    }

    public function get(string $action): RequestHandlerInterface
    {
        if (!isset($this->handlers[$action])) {
            $this->create($action);
        }

        return $this->handlers[$action];
    }

    public function getHandlersPool(): array
    {
        return $this->handlersPool;
    }
}
