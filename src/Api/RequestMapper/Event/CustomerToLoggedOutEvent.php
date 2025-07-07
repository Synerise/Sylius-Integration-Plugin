<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Event;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Synerise\Api\V4\Models\Client;
use Synerise\Api\V4\Models\LoggedOutEvent;
use Synerise\Sdk\Api\RequestBody\Events\LoggedOutBuilder;
use Synerise\SyliusIntegrationPlugin\Event\Model\BeforeLogoutRequestEvent;

class CustomerToLoggedOutEvent
{
    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function prepare(CustomerInterface $customer, Client $client): LoggedOutEvent
    {
        $client->setEmail($customer->getEmail());
        $client->setCustomId((string) $customer->getId());

        $loggedOutEvent = LoggedOutBuilder::initialize($client)->build();

        $event = new BeforeLogoutRequestEvent($loggedOutEvent, $customer);
        $this->eventDispatcher->dispatch($event, BeforeLogoutRequestEvent::NAME);

        return $event->getLoggedOutEvent();
    }
}
