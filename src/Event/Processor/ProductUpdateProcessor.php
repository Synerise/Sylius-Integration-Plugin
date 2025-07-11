<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event\Processor;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Resource\ProductToAddItemMapper;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Event\Handler\EventHandlerResolver;
use Webmozart\Assert\Assert;

class ProductUpdateProcessor implements ProductProcessorInterface
{
    public function __construct(
        private ProductToAddItemMapper $mapper,
        private ChannelConfigurationFactory $channelConfigurationFactory,
        private EventHandlerResolver $eventHandlerResolver,
    ) {
    }

    public function process(ProductInterface $product): void
    {
        foreach ($product->getChannels() as $channel) {
            Assert::isInstanceOf($channel, ChannelInterface::class);

            $configuration = $this->channelConfigurationFactory->get();
            if (!$type = $configuration?->getEventHandlerType('product.update')) {
                return;
            }

            $this->eventHandlerResolver->get($type)->processEvent(
                'product.update',
                $this->mapper->prepare($product, 'live', $channel),
                $channel->getId(),
            );
        }
    }
}
