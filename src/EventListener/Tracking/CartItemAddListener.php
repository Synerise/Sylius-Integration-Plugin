<?php

namespace Synerise\SyliusIntegrationPlugin\EventListener\Tracking;

use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Synerise\SyliusIntegrationPlugin\EventProcessor\CartItemAddProcessor;
use Webmozart\Assert\Assert;

final readonly class CartItemAddListener
{
    public function __construct(
        private CartItemAddProcessor $processor,
    ) {
    }

    public function __invoke(GenericEvent $event): void
    {
        $addToCartCommand = $event->getSubject();
        Assert::isInstanceOf($addToCartCommand, AddToCartCommandInterface::class);

        $this->processor->process($addToCartCommand);
    }
}
