<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event\Model;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Synerise\Api\V4\Models\LoggedOutEvent;

class BeforeLogoutRequestEvent extends Event
{
    public const NAME = 'synerise.customer.logout.before_send';

    public function __construct(
        private LoggedOutEvent $loggedOutEvent,
        private readonly CustomerInterface $customer,
    ) {
    }

    public function getLoggedOutEvent(): LoggedOutEvent
    {
        return $this->loggedOutEvent;
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }
}
