<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event\Listener;

use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Synerise\SyliusIntegrationPlugin\Event\Processor\OrderProcessor;
use Webmozart\Assert\Assert;

class OrderWorkflowListener
{
    public function __construct(
        private LoggerInterface $syneriseLogger,
        private OrderProcessor $orderProcessor,
    ) {
    }

    public function __invoke(CompletedEvent $event): void
    {
        try {
            /** @var OrderInterface $order */
            $order = $event->getSubject();
            Assert::isInstanceOf($order, OrderInterface::class);

            $this->orderProcessor->process($order);
        } catch (\Throwable $e) {
            $this->syneriseLogger->error($e);
        }
    }
}
