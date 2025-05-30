<?php

namespace Synerise\SyliusIntegrationPlugin\Event\Listener;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Synerise\SyliusIntegrationPlugin\Event\Processor\CartStatusProcessor;
use Webmozart\Assert\Assert;

class CartStatusListener
{
    public function __construct(
        private CartContextInterface $cartContext,
        private CartStatusProcessor $processor
    ) {
    }

    /**
     * @throws \Exception|ExceptionInterface
     */
    public function __invoke(GenericEvent $event): void
    {
        $cart = $this->cartContext->getCart();
        Assert::isInstanceOf($cart, OrderInterface::class);
        $this->processor->process($cart);
    }
}
