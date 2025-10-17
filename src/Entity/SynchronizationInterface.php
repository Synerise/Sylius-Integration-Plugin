<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface SynchronizationInterface extends ResourceInterface
{
    public function getId(): ?int;

    public function getType(): ?SynchronizationDataType;

    public function setType(?SynchronizationDataType $type): void;

    public function getStatus(): ?SynchronizationStatus;

    public function setStatus(?SynchronizationStatus $status): void;

    public function getConfigurationSnapshot(): ?string;

    public function setConfigurationSnapshot(?string $configurationSnapshot): void;

    public function getChannel(): ?ChannelInterface;

    public function setChannel(?ChannelInterface $channel): void;

    public function getCreatedAt(): ?\DateTimeInterface;

    public function setCreatedAt(\DateTimeImmutable $createdAt): void;

    public function getUpdatedAt(): ?\DateTimeInterface;

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void;

    public function getSent(): ?int;

    public function setSent(?int $sent): void;

    public function getTotal(): ?int;

    public function setTotal(?int $total): void;

    public function getSinceWhen(): ?\DateTimeImmutable;

    public function setSinceWhen(?\DateTimeImmutable $sinceWhen): void;

    public function getUntilWhen(): ?\DateTimeImmutable;

    public function setUntilWhen(?\DateTimeImmutable $untilWhen): void;
}
