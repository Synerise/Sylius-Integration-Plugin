<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Processor;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Synerise\Api\V4\Events\LoggedOut\LoggedOutPostRequestBody;
use Synerise\Api\V4\Models\Client;
use Synerise\Api\V4\Models\LoggedOutEvent;
use Synerise\Sdk\Api\RequestBody\Events\LoggedOutBuilder;
use Synerise\Sdk\Exception\NotFoundException;
use Synerise\Sdk\Tracking\IdentityManager;
use Synerise\SyliusIntegrationPlugin\Event\BeforeLogoutRequestEvent;
use Synerise\SyliusIntegrationPlugin\Service\EventService;

class CustomerLogoutProcessor implements CustomerProcessorInterface
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
        $loggedOutRequestBody = $this->prepareLoggedOutRequestBody($customer);

        $channelId = $this->channel->getChannel()->getId();
        $this->eventService->processEvent(LoggedOutBuilder::ACTION, $loggedOutRequestBody, (string)$channelId);
    }

    private function prepareLoggedOutRequestBody(CustomerInterface $customer): LoggedOutEvent
    {
        $client = new Client();

        try {
            $client = $this->identityManager->getClient();
        } catch (NotFoundException $exception) {
        }

        $client->setEmail($customer->getEmail());
        $client->setCustomId((string)$customer->getId());

        $loggedOutEvent = LoggedOutBuilder::initialize($client)->build();

        $event = new BeforeLogoutRequestEvent($loggedOutEvent, $customer);
        $this->eventDispatcher->dispatch($event, BeforeLogoutRequestEvent::NAME);

        return $event->getLoggedOutEvent();
    }
}
