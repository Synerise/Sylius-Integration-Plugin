<?php

namespace Synerise\SyliusIntegrationPlugin\Entity;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;

class SynchronizationStatusOrder
{
    private OrderInterface $order;

    private ChannelInterface $channel;

    private \DateTimeInterface $updatedAt;

    public function getOrder(): OrderInterface
    {
        return $this->order;
    }
    public function setOrder(OrderInterface $order): void
    {
        $this->order = $order;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(?ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

}
