<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface ChannelConfigurationInterface extends ResourceInterface
{
    public function getId(): ?int;

    public function getChannel(): ?ChannelInterface;

    public function setChannel(?ChannelInterface $channel): static;

    public function getWorkspace(): ?WorkspaceInterface;

    public function setWorkspace(?WorkspaceInterface $workspace): static;


    public function isTrackingEnabled(): ?bool;

    public function setTrackingEnabled(?bool $trackingEnabled): void;

    public function getTrackingCode(): ?string;

    public function setTrackingCode(?string $trackingCode): void;
}
