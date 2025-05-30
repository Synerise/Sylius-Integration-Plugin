<?php

namespace Synerise\SyliusIntegrationPlugin\Event\Listener;

use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Synerise\SyliusIntegrationPlugin\Event\Processor\CartItemRemoveProcessor;
use Webmozart\Assert\Assert;

class CartItemRemoveListener
{
    public function __construct(
        private CartItemRemoveProcessor $processor
    ) {
    }

    public function __invoke(GenericEvent $event): void
    {
        /** @var OrderItemInterface $cartItem */
        $cartItem = $event->getSubject();

        Assert::isInstanceOf($cartItem, OrderItemInterface::class);

        $this->processor->process($cartItem);

    }
}
