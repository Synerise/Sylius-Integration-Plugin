<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Processor;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Synerise\Api\V4\Events\LoggedIn\LoggedInPostRequestBody;
use Synerise\Api\V4\Models\Client;
use Synerise\Sdk\Api\RequestBody\Events\LoggedInBuilder;
use Synerise\Sdk\Exception\NotFoundException;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Event\BeforeLoginRequestEvent;
use Synerise\SyliusIntegrationPlugin\Service\EventService;

class CustomerLoginProcessor implements CustomerProcessorInterface
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
     * @throws ExceptionInterface
     */
    public function process(CustomerInterface $customer): void
    {
        $loggedInRequestBody = $this->prepareLoggedInRequestBody($customer);

        $channelId = $this->channel->getChannel()->getId();
        $this->eventService->processEvent(LoggedInBuilder::ACTION, $loggedInRequestBody, (string)$channelId);
    }

    private function prepareLoggedInRequestBody(CustomerInterface $customer): LoggedInPostRequestBody
    {
        $client = new Client();

        try {
            $client = $this->identityManager->getClient();
        } catch (NotFoundException $exception) {
        }

        $client->setEmail($customer->getEmail());
        $client->setCustomId((string)$customer->getId());

        $loggedInPostRequestBody = LoggedInBuilder::initialize($client)->build();

        $event = new BeforeLoginRequestEvent($loggedInPostRequestBody, $customer);
        $this->eventDispatcher->dispatch($event, BeforeLoginRequestEvent::NAME);

        return $event->getLoggedInRequest();
    }
}
