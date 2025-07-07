<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Event;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Synerise\Api\V4\Models\Client;
use Synerise\Api\V4\Models\LoggedInEvent;
use Synerise\Sdk\Api\RequestBody\Events\LoggedInBuilder;
use Synerise\SyliusIntegrationPlugin\Event\Model\BeforeLoginRequestEvent;

class CustomerToLoggedInEvent
{
    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function prepare(CustomerInterface $customer, Client $client): LoggedInEvent
    {
        $client->setEmail($customer->getEmail());
        $client->setCustomId((string) $customer->getId());

        $loggedInEvent = LoggedInBuilder::initialize($client)->build();

        $event = new BeforeLoginRequestEvent($loggedInEvent, $customer);
        $this->eventDispatcher->dispatch($event, BeforeLoginRequestEvent::NAME);

        return $event->getLoggedInEvent();
    }
}
