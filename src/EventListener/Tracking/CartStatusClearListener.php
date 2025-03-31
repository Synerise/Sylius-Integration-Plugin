<?php

namespace Synerise\SyliusIntegrationPlugin\EventListener\Tracking;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Synerise\Api\V4\Events\Custom\CustomPostRequestBody;
use Synerise\Sdk\Api\RequestBody\Events\CartStatusBuilder;
use Synerise\Sdk\Exception\NotFoundException;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Service\EventService;

class CartStatusClearListener
{
    private ChannelContextInterface $channel;

    private IdentityManager $identityManager;

    private EventService $eventService;

    public function __construct(
        ChannelContextInterface $channel,
        IdentityManager $identityManagerProvider,
        EventService $eventService
    ) {
        $this->channel = $channel;
        $this->identityManager = $identityManagerProvider;
        $this->eventService = $eventService;
    }

    /**
     * @throws NotFoundException|ExceptionInterface
     */
    public function process(GenericEvent $event): void
    {
        $this->eventService->processEvent(
            CartStatusBuilder::ACTION,
            $this->prepareCartStatusRequestBody(),
            $this->channel->getChannel()->getId()
        );
    }

    /**
     * @throws NotFoundException
     */
    private function prepareCartStatusRequestBody(): CustomPostRequestBody
    {
        return CartStatusBuilder::initialize($this->identityManager->getClient())
            ->setTotalAmount(0)
            ->setTotalQuantity(0.0)
            ->setProducts([])
            ->build();
    }
}
