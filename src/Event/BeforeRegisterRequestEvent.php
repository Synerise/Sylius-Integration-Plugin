<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Synerise\Api\V4\Events\LoggedIn\LoggedInPostRequestBody;
use Synerise\Api\V4\Events\Registered\RegisteredPostRequestBody;

class BeforeRegisterRequestEvent extends Event
{
    public const NAME = 'synerise.customer.register.before_send';

    public function __construct(
        private RegisteredPostRequestBody $registeredRequest,
        private readonly CustomerInterface $customer
    )
    {
    }

    public function getRegisteredRequest(): RegisteredPostRequestBody
    {
        return $this->registeredRequest;
    }

    public function getCustomer(): CustomerInterface {
        return $this->customer;
    }

}
