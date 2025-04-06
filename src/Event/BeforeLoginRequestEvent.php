<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Synerise\Api\V4\Events\LoggedIn\LoggedInPostRequestBody;

class BeforeLoginRequestEvent extends Event
{
    public const NAME = 'synerise.customer.login.before_send';

    public function __construct(
        private LoggedInPostRequestBody $loggedInRequest,
        private readonly CustomerInterface $customer
    )
    {
    }

    public function getLoggedInRequest(): LoggedInPostRequestBody
    {
        return $this->loggedInRequest;
    }

    public function getCustomer(): CustomerInterface {
        return $this->customer;
    }

}
