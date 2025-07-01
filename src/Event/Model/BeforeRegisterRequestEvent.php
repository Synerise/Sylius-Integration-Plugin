<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event\Model;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Synerise\Api\V4\Models\RegisteredEvent;

class BeforeRegisterRequestEvent extends Event
{
    public const NAME = 'synerise.customer.register.before_send';

    public function __construct(
        private RegisteredEvent $registeredEvent,
        private readonly CustomerInterface $customer,
    ) {
    }

    public function getRegisteredEvent(): RegisteredEvent
    {
        return $this->registeredEvent;
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }
}
