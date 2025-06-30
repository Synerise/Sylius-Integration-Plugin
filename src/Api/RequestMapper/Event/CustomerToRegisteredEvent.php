<?php

namespace Synerise\SyliusIntegrationPlugin\Api\RequestMapper\Event;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Synerise\Api\V4\Models\Client;
use Synerise\Api\V4\Models\RegisteredEvent;
use Synerise\Sdk\Api\RequestBody\Events\RegisteredBuilder;
use Synerise\SyliusIntegrationPlugin\Event\Model\BeforeRegisterRequestEvent;

class CustomerToRegisteredEvent
{
    public function __construct(private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function prepare(CustomerInterface $customer, Client $client): RegisteredEvent
    {
        $client->setEmail($customer->getEmail());
        $client->setCustomId((string)$customer->getId());

        $registeredEvent = RegisteredBuilder::initialize($client)->build();

        $event = new BeforeRegisterRequestEvent($registeredEvent, $customer);
        $this->eventDispatcher->dispatch($event, BeforeRegisterRequestEvent::NAME);

        return $event->getRegisteredEvent();
    }
}
