<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Processor;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Synerise\Api\V4\Events\Registered\RegisteredPostRequestBody;
use Synerise\Api\V4\Models\Client;
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
     * @throws NotFoundException
     * @throws ExceptionInterface
     */
    public function process(CustomerInterface $customer): void
    {
        $clientRegisterRequestBody = $this->prepareCustomerRegisteredRequestBody($customer);

        $channelId = $this->channel->getChannel()->getId();
        $this->eventService->processEvent(RegisteredBuilder::ACTION, $clientRegisterRequestBody, $channelId);
    }

    private function prepareCustomerRegisteredRequestBody(CustomerInterface $customer): RegisteredPostRequestBody
    {
        $client = new Client();

        try {
            $client = $this->identityManager->getClient();
        } catch (NotFoundException $exception) {
        }

        $client->setEmail($customer->getEmail());
        $client->setCustomId((string)$customer->getId());

        $beforeRegisteredRequestBody = RegisteredBuilder::initialize($client)->build();

        $event = new BeforeRegisterRequestEvent($beforeRegisteredRequestBody, $customer);
        $this->eventDispatcher->dispatch($event, BeforeRegisterRequestEvent::NAME);

        return $event->getRegisteredRequest();
    }
}
