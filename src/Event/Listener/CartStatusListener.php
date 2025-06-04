<?php

namespace Synerise\SyliusIntegrationPlugin\Event\Listener;

use Psr\Log\LoggerInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Synerise\SyliusIntegrationPlugin\Event\Processor\CartStatusProcessor;

final readonly class CartStatusListener
{
    public function __construct(
        private LoggerInterface $syneriseLogger,
        private CartStatusProcessor $processor,
        private ?CartContextInterface $cartContext = null
    ) {
    }

    public function __invoke(GenericEvent $event): void
    {
        try {
            /** @var \Sylius\Component\Core\Model\OrderInterface|null $cart */
            $cart = $this->cartContext?->getCart();

            $this->processor->process($cart);
        } catch (\Throwable $e) {
            $this->syneriseLogger->error($e);
        }
    }
}

