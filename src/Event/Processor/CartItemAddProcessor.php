<?php

namespace Synerise\SyliusIntegrationPlugin\Event\Processor;

use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Synerise\Sdk\Api\RequestBody\Events\AddedToCartBuilder;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Event\OrderItemAddToCartEventMapper;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Event\Handler\EventHandlerResolver;

class CartItemAddProcessor
{
    public function __construct(
        private OrderItemAddToCartEventMapper $mapper,
        private ChannelConfigurationFactory   $configurationFactory,
        private IdentityManager               $identityManager,
        private EventHandlerResolver          $eventHandlerResolver
    ) {
    }

    public function process(AddToCartCommandInterface $addToCartCommand): void
    {
        /** @var OrderInterface $cart */
        $cart = $addToCartCommand->getCart();

        /** @var OrderItemInterface $cartItem */
        $cartItem = $addToCartCommand->getCartItem();

        $configuration = $this->configurationFactory->get($cart->getChannel()?->getId());
        if (!$type = $configuration?->getEventHandlerType(AddedToCartBuilder::ACTION)) {
            return;
        }

        $this->eventHandlerResolver->get($type)->processEvent(
            AddedToCartBuilder::ACTION,
            $this->mapper->prepare($cartItem, $this->identityManager->getClient()),
            $cart->getChannel()?->getId()
        );
    }
}
