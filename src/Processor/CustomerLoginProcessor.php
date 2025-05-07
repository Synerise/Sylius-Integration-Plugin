<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Processor;

use Exception;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Synerise\Api\V4\Models\Client;
use Synerise\Api\V4\Models\LoggedInEvent;
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
     * @throws Exception
     */
    public function process(CustomerInterface $customer): void
    {
        $loggedInEvent = $this->prepareLoggedInEvent($customer);

        $channelId = $this->channel->getChannel()->getId();
        $this->eventService->processEvent(LoggedInBuilder::ACTION, $loggedInEvent, (string)$channelId);
    }

    private function prepareLoggedInEvent(CustomerInterface $customer): LoggedInEvent
    {
        try {
            $client = $this->identityManager->getClient();
        } catch (NotFoundException $exception) {
            $client = new Client();
        }

        $client->setEmail($customer->getEmail());
        $client->setCustomId((string)$customer->getId());

        $loggedInEvent = LoggedInBuilder::initialize($client)->build();

        $event = new BeforeLoginRequestEvent($loggedInEvent, $customer);
        $this->eventDispatcher->dispatch($event, BeforeLoginRequestEvent::NAME);

        return $event->getLoggedInEvent();
    }
}
