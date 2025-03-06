<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Core\Model\ChannelInterface;

class ChannelConfiguration implements ChannelConfigurationInterface
{
    private ?int $id = null;

    private ?ChannelInterface $channel = null;

    private ?WorkspaceInterface $workspace = null;

    private ?bool $trackingEnabled = false;

    private ?string $trackingCode = null;

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

    public function isTrackingEnabled(): ?bool
    {
        return $this->trackingEnabled;
    }

    public function setTrackingEnabled(?bool $trackingEnabled): void
    {
        $this->trackingEnabled = $trackingEnabled;
    }

    public function getTrackingCode(): ?string
    {
        return $this->trackingCode;
    }

    public function setTrackingCode(?string $trackingCode): void
    {
        $this->trackingCode = $trackingCode;
    }
}
