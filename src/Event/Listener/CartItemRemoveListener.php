<?php

namespace Synerise\SyliusIntegrationPlugin\Event\Listener;

use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Synerise\SyliusIntegrationPlugin\Event\Processor\CartItemRemoveProcessor;
use Webmozart\Assert\Assert;

class CartItemRemoveListener
{
    public function __construct(
        private LoggerInterface $syneriseLogger,
        private CartItemRemoveProcessor $processor
    ) {
    }

    public function __invoke(GenericEvent $event): void
    {
        try {
            /** @var OrderItemInterface $cartItem */
            $cartItem = $event->getSubject();

            Assert::isInstanceOf($cartItem, OrderItemInterface::class);

            $this->processor->process($cartItem);
        } catch (\Throwable $e) {
            $this->syneriseLogger->error($e);
        }
    }
}
