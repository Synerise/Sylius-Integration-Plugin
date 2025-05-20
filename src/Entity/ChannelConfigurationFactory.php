<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Context\ChannelContextInterface;

class ChannelConfigurationFactory
{
    private ChannelContextInterface $channel;

    private EntityRepository $repository;

    private array $instance = [];

    public function __construct(
        ChannelContextInterface $channel,
        EntityRepository $repository,
    ) {
        $this->channel = $channel;
        $this->repository = $repository;
    }

    public function create(?string $channelId = null): ?ChannelConfigurationInterface
    {
        if ($channelId === null) {
            $channelId = $this->channel->getChannel()->getId();
        }

        // @phpstan-ignore return.type
        return $this->repository->findOneBy(
            ['channel' => $channelId]
        );
    }

    public function get(string|int|null $channelId = null): ?ChannelConfigurationInterface
    {
        if ($channelId === null) {
            $channelId = $this->channel->getChannel()->getId();
        }

        if (!isset($this->instance[$channelId])) {
            $this->instance[$channelId] = $this->create($channelId);
        }

        return $this->instance[$channelId];
    }

    public function getWorkspace(): ?WorkspaceInterface
    {
        return $this->get()?->getWorkspace();
    }
}
