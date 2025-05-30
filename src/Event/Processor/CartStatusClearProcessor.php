<?php

namespace Synerise\SyliusIntegrationPlugin\Event\Processor;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Synerise\Sdk\Api\RequestBody\Events\AddedToCartBuilder;
use Synerise\Sdk\Api\RequestBody\Events\CartStatusBuilder;
use Synerise\Sdk\Exception\NotFoundException;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Event\OrderToCartStatusEventMapper;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\Event\Handler\EventHandlerResolver;

class CartStatusClearProcessor
{
    public function __construct(
        private OrderToCartStatusEventMapper $mapper,
        private ChannelContextInterface $channel,
        private ChannelConfigurationFactory $configurationFactory,
        private IdentityManager $identityManager,
        private EventHandlerResolver $eventHandlerResolver,
    ) {
    }

    /**
     * @throws NotFoundException
     */
    public function process(): void
    {
        $channelId = $this->channel->getChannel()->getId();
        $configuration = $this->configurationFactory->get($channelId);
        if (!$type = $configuration?->getEventHandlerType(AddedToCartBuilder::ACTION)) {
            return;
        }

        $this->eventHandlerResolver->get($type)->processEvent(
            CartStatusBuilder::ACTION,
            $this->mapper->prepare($this->identityManager->getClient()),
            $channelId
        );
    }
}
