<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\Event;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Synerise\Api\V4\Models\Profile;

class BeforeProfileRequestEvent extends Event
{
    public const NAME = 'synerise.profile.update.before_send';

    public function __construct(
        private Profile $profile,
        private CustomerInterface $customer
    )
    {
    }

    public function getProfile(): Profile
    {
        return $this->profile;
    }

    public function getCustomer(): CustomerInterface {
        return $this->customer;
    }

}
