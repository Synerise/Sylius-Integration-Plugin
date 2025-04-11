<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\EventListener\Test;

use Synerise\SyliusIntegrationPlugin\Event\BeforeLoginRequestEvent;

final readonly class SyneriseBeforeLoginRequestListener
{

    public function __invoke(BeforeLoginRequestEvent $event): void
    {
        $customer = $event->getCustomer();
        $loggedInRequest = $event->getLoggedInRequest();
        $params = $loggedInRequest->getParams();

        $params->setAdditionalData([
            "isVerified" => $customer->getUser()->getVerifiedAt() !== null
        ]);
    }
}
