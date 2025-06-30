<?php

namespace Synerise\SyliusIntegrationPlugin\Event\Listener;

use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Synerise\SyliusIntegrationPlugin\Event\Processor\ProductProcessorInterface;
use Webmozart\Assert\Assert;

final readonly class ProductUpdatedListener
{
    public function __construct(
        private LoggerInterface $syneriseLogger,
        private ProductProcessorInterface $productProcessor
    )
    {
    }

    public function __invoke(GenericEvent $event): void
    {
        try {
            /** @var ProductInterface $product */
            $product = $event->getSubject();
            Assert::isInstanceOf($product, ProductInterface::class);

            $this->productProcessor->process($product);
        } catch (\Throwable $e) {
            $this->syneriseLogger->error($e);
        }
    }

}
