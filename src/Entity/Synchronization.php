<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

class Synchronization implements ResourceInterface
{
    private ?int $id = null;

    private ?ChannelInterface $channel = null;

    private ?string $configurationSnapshot = null;

    private ?SynchronizationDataType $type = null;

    private ?SynchronizationStatus $status = null;

    private ?int $sent = null;

    private ?int $total = null;

    private ?\DateTimeImmutable $sinceWhen = null;

    private ?\DateTimeImmutable $untilWhen = null;

    private ?\DateTimeImmutable $createdAt = null;

    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?SynchronizationDataType
    {
        return $this->type;
    }

    public function setType(?SynchronizationDataType $type): void
    {
        $this->type = $type;
    }

    public function getStatus(): ?SynchronizationStatus
    {
        return $this->status;
    }

    public function setStatus(?SynchronizationStatus $status): void
    {
        $this->status = $status;
    }

    public function getConfigurationSnapshot(): ?string
    {
        return $this->configurationSnapshot;
    }

    public function setConfigurationSnapshot(?string $configurationSnapshot): void
    {
        $this->configurationSnapshot = $configurationSnapshot;
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(?ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getSent(): ?int
    {
        return $this->sent;
    }

    public function setSent(?int $sent): void
    {
        $this->sent = $sent;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(?int $total): void
    {
        $this->total = $total;
    }

    public function getSinceWhen(): ?\DateTimeImmutable
    {
        return $this->sinceWhen;
    }

    public function setSinceWhen(?\DateTimeImmutable $sinceWhen): void
    {
        $this->sinceWhen = $sinceWhen;
    }

    public function getUntilWhen(): ?\DateTimeImmutable
    {
        return $this->untilWhen;
    }

    public function setUntilWhen(?\DateTimeImmutable $untilWhen): void
    {
        $this->untilWhen = $untilWhen;
    }
}
