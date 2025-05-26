<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Synerise\Api\Catalogs\Models\AddItem;

class ProductUpdateRequestEvent extends Event
{
    public const NAME = 'synerise.product.update.before_send';

    public function __construct(
        private AddItem $addItemEvent,
        private readonly ProductInterface $product,
        private readonly ChannelInterface $channel
    ) {
    }

    public function getAddItem(): AddItem
    {
        return $this->addItemEvent;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }
}
