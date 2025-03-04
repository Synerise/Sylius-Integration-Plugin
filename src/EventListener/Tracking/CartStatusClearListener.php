<?php

namespace Synerise\SyliusIntegrationPlugin\EventListener\Tracking;

use Symfony\Component\EventDispatcher\GenericEvent;
use Synerise\Api\V4\Events\Custom\CustomPostRequestBody;
use Synerise\Sdk\Api\ClientBuilder;
use Synerise\Sdk\Api\RequestBody\Events\CartStatusBuilder;
use Synerise\Sdk\Tracking\IdentityManager;

class CartStatusClearListener
{
    private ClientBuilder $clientBuilder;

    private IdentityManager $identityManager;

    public function __construct(
        ClientBuilder $clientBuilder,
        IdentityManager $identityManager
    ) {
        $this->clientBuilder = $clientBuilder;
        $this->identityManager = $identityManager;
    }

    public function process(GenericEvent $event): void
    {
        $requestBody = $this->prepareCartStatusRequestBody();
        $this->clientBuilder->v4()->events()->custom()->post($requestBody)->wait();
    }

    private function prepareCartStatusRequestBody(): CustomPostRequestBody
    {
        return CartStatusBuilder::initialize($this->identityManager->getClient())
            ->setTotalAmount(0)
            ->setTotalQuantity(0.0)
            ->setProducts([])
            ->build();
    }
}
