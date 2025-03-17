<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface ChannelConfigurationInterface extends ResourceInterface
{
    public function getId(): ?int;

    public function getChannel(): ?ChannelInterface;

    public function setChannel(?ChannelInterface $channel): void;

    public function getWorkspace(): ?WorkspaceInterface;

    public function setWorkspace(?WorkspaceInterface $workspace): void;

    public function isTrackingEnabled(): bool;

    public function setTrackingEnabled(bool $trackingEnabled): void;

    public function getTrackingCode(): ?string;

    public function setTrackingCode(?string $trackingCode): void;

    public function getCookieDomain(): ?string;

    public function setCookieDomain(?string $cookieDomain): void;

    public function isCustomPageVisit(): bool;

    public function setCustomPageVisit(bool $customPageVisit): void;

    public function isVirtualPage(): bool;

    public function setVirtualPage(bool $virtualPage): void;
}
