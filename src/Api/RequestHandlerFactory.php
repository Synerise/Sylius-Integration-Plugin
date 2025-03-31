<?php

namespace Synerise\SyliusIntegrationPlugin\Api;

use Synerise\SyliusIntegrationPlugin\Api\RequestHandler\RequestHandler;
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

    public function create(string $action): RequestHandler
    {
        Assert::keyExists($this->handlersPool, $action);
        Assert::classExists($this->handlersPool[$action]);
        Assert::implementsInterface($this->handlersPool[$action], RequestHandler::class);

        $this->handlers[$action] = new $this->handlersPool[$action]($this->clientBuilderFactory);

        return $this->handlers[$action];
    }

    public function get(string $action): RequestHandler
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
