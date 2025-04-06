<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Synerise\Api\V4\Clients\ClientsPostRequestBody;

class BeforeCustomerRequestEvent extends Event
{
    public const NAME = 'synerise.customer.update.before_send';

    public function __construct(
        private ClientsPostRequestBody $client,
        private readonly CustomerInterface $customer
    )
    {
    }

    public function getClient(): ClientsPostRequestBody
    {
        return $this->client;
    }

    public function getCustomer(): CustomerInterface {
        return $this->customer;
    }

}
