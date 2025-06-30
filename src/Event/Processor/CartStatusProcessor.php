<?php

namespace Synerise\SyliusIntegrationPlugin\Event\Processor;

use Sylius\Component\Core\Model\OrderInterface;
use Synerise\Sdk\Api\RequestBody\Events\CartStatusBuilder;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Event\OrderToCartStatusEventMapper;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Event\Handler\EventHandlerResolver;

class CartStatusProcessor
{
    public function __construct(
        private OrderToCartStatusEventMapper $mapper,
        private ChannelConfigurationFactory $configurationFactory,
        private IdentityManager $identityManager,
        private EventHandlerResolver $eventHandlerResolver,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function process(?OrderInterface $cart = null): void
    {
        $configuration = $this->configurationFactory->get($cart?->getChannel()?->getId());
        if (!$type = $configuration?->getEventHandlerType(CartStatusBuilder::ACTION)) {
            return;
        }

        $this->eventHandlerResolver->get($type)->processEvent(
            CartStatusBuilder::ACTION,
            $this->mapper->prepare($this->identityManager->getClient(), $cart),
            $configuration->getChannel()?->getId()
        );
    }
}
