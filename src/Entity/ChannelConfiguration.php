<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Core\Model\ChannelInterface;

class ChannelConfiguration implements ChannelConfigurationInterface
{
    private ?int $id = null;

    private ?ChannelInterface $channel = null;

    private ?WorkspaceInterface $workspace = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(?ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

    public function getWorkspace(): ?WorkspaceInterface
    {
        return $this->workspace;
    }

    public function setWorkspace(?WorkspaceInterface $workspace): void
    {
        $this->workspace = $workspace;
    }
}
