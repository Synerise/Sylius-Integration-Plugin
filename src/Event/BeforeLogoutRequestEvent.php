<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Synerise\Api\V4\Events\LoggedOut\LoggedOutPostRequestBody;

class BeforeLogoutRequestEvent extends Event
{
    public const NAME = 'synerise.customer.logout.before_send';

    public function __construct(
        private LoggedOutPostRequestBody   $loggedOutRequest,
        private readonly CustomerInterface $customer
    )
    {
    }

    public function getLoggedOutRequest(): LoggedOutPostRequestBody
    {
        return $this->loggedOutRequest;
    }

    public function getCustomer(): CustomerInterface
    {
        return $this->customer;
    }

}
