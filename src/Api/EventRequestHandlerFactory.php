<?php

namespace Synerise\SyliusIntegrationPlugin\Api;

use Synerise\SyliusIntegrationPlugin\Api\RequestHandler\RequestHandlerInterface;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Entity\SynchronizationConfigurationFactory;
use Webmozart\Assert\Assert;

class EventRequestHandlerFactory
{
    /**
     * @var RequestHandlerInterface[]
     */
    private array $handlers = [];

    public function  __construct(
        private ClientBuilderFactory $clientBuilderFactory,
        private ChannelConfigurationFactory $channelConfigurationFactory,
        private SynchronizationConfigurationFactory $synchronizationConfigurationFactory,
        private array $handlersPool
    ){
    }

    public function create(string $action): RequestHandlerInterface
    {
        Assert::keyExists($this->handlersPool, $action);
        Assert::classExists($this->handlersPool[$action]);
        Assert::implementsInterface($this->handlersPool[$action], RequestHandlerInterface::class);

        $this->handlers[$action] = new $this->handlersPool[$action](
            $this->clientBuilderFactory,
            $this->channelConfigurationFactory,
            $this->synchronizationConfigurationFactory
        );

        return $this->handlers[$action];
    }

    public function get(string $action): RequestHandlerInterface
    {
        if (!isset($this->handlers[$action])) {
            $this->create($action);
        }

        return $this->handlers[$action];
    }

    /**
     * @return string[]
     */
    public function getHandlersPool(): array
    {
        return $this->handlersPool;
    }
}
