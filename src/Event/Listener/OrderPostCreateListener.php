<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event\Listener;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Synerise\SyliusIntegrationPlugin\Event\Processor\OrderProcessor;
use Webmozart\Assert\Assert;

class OrderPostCreateListener
{
    public function __construct(
        private OrderProcessor $orderProcessor,
    )
    {
    }

    public function __invoke(GenericEvent $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();
        Assert::isInstanceOf($order, OrderInterface::class);

        $this->orderProcessor->process($order);
    }
}
