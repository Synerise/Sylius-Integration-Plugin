<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

class SynchronizationStatusProduct implements SynchronizationStatusInterface
{
    private ?int $id = null;

    private ?ChannelInterface $channel = null;

    private ?array $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(?ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

    public function getUpdatedAt(): ?array
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?array $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

}
