<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event\Model;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Synerise\Api\V4\Models\LoggedInEvent;

class BeforeLoginRequestEvent extends Event
{
    public const NAME = 'synerise.customer.login.before_send';

    public function __construct(
        private LoggedInEvent $loggedInEvent,
        private readonly CustomerInterface $customer
    )
    {
    }

    public function getLoggedInEvent(): LoggedInEvent
    {
        return $this->loggedInEvent;
    }

    public function getCustomer(): CustomerInterface {
        return $this->customer;
    }

}
