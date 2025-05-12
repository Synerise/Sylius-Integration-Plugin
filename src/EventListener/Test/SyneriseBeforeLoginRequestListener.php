<?php

declare(strict_types=1);

namespace Synerise\SyliusIntegrationPlugin\EventListener\Test;

use Synerise\SyliusIntegrationPlugin\Event\BeforeLoginRequestEvent;
use Webmozart\Assert\Assert;

final readonly class SyneriseBeforeLoginRequestListener
{

    public function __invoke(BeforeLoginRequestEvent $event): void
    {
        $customer = $event->getCustomer();
        $loggedInRequest = $event->getLoggedInEvent();
        $params = $loggedInRequest->getParams();

        Assert::notNull($params);

        $params->setAdditionalData([
            "isVerified" => $customer->getUser()?->getVerifiedAt() !== null
        ]);
    }
}
