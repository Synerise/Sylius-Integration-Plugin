<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Processor;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Synerise\Api\V4\Events\Registered\RegisteredPostRequestBody;
use Synerise\Api\V4\Models\Client;
use Synerise\Api\V4\Models\RegisteredEvent;
use Synerise\Sdk\Api\RequestBody\Events\RegisteredBuilder;
use Synerise\Sdk\Exception\NotFoundException;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Event\BeforeRegisterRequestEvent;
use Synerise\SyliusIntegrationPlugin\Service\EventService;

class CustomerRegisterProcessor implements CustomerProcessorInterface
{
    public function __construct(
        private ChannelContextInterface  $channel,
        private IdentityManager          $identityManager,
        private EventService             $eventService,
        private EventDispatcherInterface $eventDispatcher
    )
    {
    }

    /**
     * @throws \Exception
     */
    public function process(CustomerInterface $customer): void
    {
        $registeredEvent = $this->prepareCustomerRegisteredRequestBody($customer);

        $channelId = $this->channel->getChannel()->getId();
        $this->eventService->processEvent(RegisteredBuilder::ACTION, $registeredEvent, (string)$channelId);
    }

    private function prepareCustomerRegisteredRequestBody(CustomerInterface $customer): RegisteredEvent
    {
        $client = new Client();

        try {
            $client = $this->identityManager->getClient();
        } catch (NotFoundException $exception) {
        }

        $client->setEmail($customer->getEmail());
        $client->setCustomId((string)$customer->getId());

        $registeredEvent = RegisteredBuilder::initialize($client)->build();

        $event = new BeforeRegisterRequestEvent($registeredEvent, $customer);
        $this->eventDispatcher->dispatch($event, BeforeRegisterRequestEvent::NAME);

        return $event->getRegisteredEvent();
    }
}
