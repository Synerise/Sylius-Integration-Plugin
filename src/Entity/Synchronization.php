<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Core\Model\ChannelInterface;

class Synchronization implements SynchronizationInterface
{
    private ?int $id = null;

    private ?ChannelInterface $channel = null;

    private ?array $dataTypes = null;

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

    public function getDataTypes(): ?array
    {
        return $this->dataTypes;
    }

    public function setDataTypes(?array $dataTypes): void
    {
        $this->dataTypes = $dataTypes;
    }
}
