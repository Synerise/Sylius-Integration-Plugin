<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event\Listener;

use Psr\Log\LoggerInterface;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Synerise\SyliusIntegrationPlugin\Event\Processor\CartItemAddProcessor;
use Webmozart\Assert\Assert;

final readonly class CartItemAddListener
{
    public function __construct(
        private LoggerInterface $syneriseLogger,
        private CartItemAddProcessor $processor,
    ) {
    }

    public function __invoke(GenericEvent $event): void
    {
        try {
            $addToCartCommand = $event->getSubject();
            Assert::isInstanceOf($addToCartCommand, AddToCartCommandInterface::class);
            $this->processor->process($addToCartCommand);
        } catch (\Throwable $e) {
            $this->syneriseLogger->error($e);
        }
    }
}
