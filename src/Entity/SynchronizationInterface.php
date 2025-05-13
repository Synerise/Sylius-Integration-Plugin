<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface SynchronizationInterface extends ResourceInterface
{
    public function getId(): ?int;

    public function setId(?int $id): void;

    public function getChannel(): ?ChannelInterface;

    public function setChannel(?ChannelInterface $channel): void;

    public function getDataTypes(): ?array;

    public function setDataTypes(?array $dataTypes): void;
}
