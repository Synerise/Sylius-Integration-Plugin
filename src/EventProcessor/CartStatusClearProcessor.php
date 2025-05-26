<?php

namespace Synerise\SyliusIntegrationPlugin\EventProcessor;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Synerise\Api\V4\Models\CustomEvent;
use Synerise\Sdk\Api\RequestBody\Events\AddedToCartBuilder;
use Synerise\Sdk\Api\RequestBody\Events\CartStatusBuilder;
use Synerise\Sdk\Exception\NotFoundException;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Entity\ChannelConfigurationFactory;
use Synerise\SyliusIntegrationPlugin\EventHandler\EventHandlerFactory;

class CartStatusClearProcessor
{
    public function __construct(
        private ChannelContextInterface $channel,
        private ChannelConfigurationFactory $configurationFactory,
        private IdentityManager $identityManager,
        private EventHandlerFactory $eventHandlerFactory,
        private EventDispatcherInterface $eventDispatcher
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

        $this->eventHandlerFactory->getHandlerByType($type)->processEvent(
            CartStatusBuilder::ACTION,
            $this->prepareCartStatusRequestBody(),
            $channelId,
            []
        );
    }

    /**
     * @throws NotFoundException
     */
    private function prepareCartStatusRequestBody(): CustomEvent
    {
        $customEvent = CartStatusBuilder::initialize($this->identityManager->getClient())
            ->setTotalAmount(0)
            ->setTotalQuantity(0.0)
            ->setProducts([])
            ->build();

        $genericEvent = new GenericEvent($customEvent);

        $this->eventDispatcher->dispatch(
            $genericEvent,
            sprintf('synerise.%s.prepare', CartStatusBuilder::ACTION)
        );

        // @phpstan-ignore return.type
        return $genericEvent->getSubject();
    }
}
