<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Synerise\SyliusIntegrationPlugin\Repository\ChannelConfigurationRepository;

class ChannelConfigurationFactory
{
    private ChannelContextInterface $channel;

    private ChannelConfigurationRepository $repository;

    private ?ChannelConfigurationInterface $instance = null;

    public function __construct(
        ChannelContextInterface $channel,
        ChannelConfigurationRepository $repository,
    ) {
        $this->channel = $channel;
        $this->repository = $repository;
    }

    public function create(): ?ChannelConfigurationInterface
    {
        return $this->getConfigurationByChannel();
    }

    public function get(): ?ChannelConfigurationInterface
    {
        if (!$this->instance) {
            $this->instance = $this->create();
        }

        return $this->instance;
    }

    public function getWorkspace(): ?WorkspaceInterface
    {
        return $this->get()?->getWorkspace();
    }

    /**
     * @return ChannelConfigurationInterface|null
     */
    private function getConfigurationByChannel(): ?ChannelConfigurationInterface
    {
        // @phpstan-ignore return.type
        return $this->repository->findOneBy(
            ['channel' => $this->channel->getChannel()]
        );
    }
}
