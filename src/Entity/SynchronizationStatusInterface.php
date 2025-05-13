<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface SynchronizationStatusInterface extends ResourceInterface
{
    public function getId(): ?int;

    public function setId(?int $id): void;

    public function getChannel(): ?ChannelInterface;

    public function setChannel(?ChannelInterface $channel): void;

    public function getUpdatedAt(): ?array;

    public function setUpdatedAt(?array $updatedAt): void;
}
