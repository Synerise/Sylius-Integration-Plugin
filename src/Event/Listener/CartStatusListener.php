<?php

namespace Synerise\SyliusIntegrationPlugin\Event\Listener;

use Psr\Log\LoggerInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Synerise\SyliusIntegrationPlugin\Event\Processor\CartStatusProcessor;

final readonly class CartStatusListener
{
    public function __construct(
        private LoggerInterface $logger,
        private CartStatusProcessor $processor,
        private ?CartContextInterface $cartContext = null
    ) {
    }

    public function __invoke(GenericEvent $event): void
    {
        try {
            $this->processor->process($this->cartContext?->getCart());
        } catch (\Throwable $e) {
            $this->logger->error($e);
        }
    }
}

