<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event\Processor;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Synerise\Sdk\Api\RequestBody\Events\AddedToCartBuilder;
use Synerise\Sdk\Api\RequestBody\Events\RemovedFromCartBuilder;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Event\OrderItemRemoveToCartEventMapper;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Event\Handler\EventHandlerResolver;
use Webmozart\Assert\Assert;

class CartItemRemoveProcessor
{
    public function __construct(
        private OrderItemRemoveToCartEventMapper $mapper,
        private ChannelConfigurationFactory $configurationFactory,
        private IdentityManager $identityManager,
        private EventHandlerResolver $eventHandlerResolver,
    ) {
    }

    public function process(OrderItemInterface $cartItem): void
    {
        Assert::isInstanceOf($cartItem, OrderItemInterface::class);

        /** @var OrderInterface $cart */
        $cart = $cartItem->getOrder();

        $configuration = $this->configurationFactory->get($cart->getChannel()?->getId());
        if (!$type = $configuration?->getEventHandlerType(AddedToCartBuilder::ACTION)) {
            return;
        }

        $this->eventHandlerResolver->get($type)->processEvent(
            RemovedFromCartBuilder::ACTION,
            $this->mapper->prepare($cartItem, $this->identityManager->getClient()),
            $cart->getChannel()?->getId(),
        );
    }
}
