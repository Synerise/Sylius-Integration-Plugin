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

    public function setChannel(?ChannelInterface $channel): static
    {
        $this->channel = $channel;

        return $this;
    }

    public function getWorkspace(): ?WorkspaceInterface
    {
        return $this->workspace;
    }

    public function setWorkspace(?WorkspaceInterface $workspace): static
    {
        $this->workspace = $workspace;

        return $this;
    }
}
