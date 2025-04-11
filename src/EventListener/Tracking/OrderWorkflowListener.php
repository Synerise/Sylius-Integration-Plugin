<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\EventListener\Tracking;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Synerise\SyliusIntegrationPlugin\Processor\OrderProcessor;
use Webmozart\Assert\Assert;

class OrderWorkflowListener
{
    public function __construct(
        private OrderProcessor $orderProcessor,
    )
    {
    }

    public function __invoke(CompletedEvent $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();
        Assert::isInstanceOf($order, OrderInterface::class);

        $this->orderProcessor->process($order);
    }
}
