<?php

namespace Synerise\SyliusIntegrationPlugin\EventListener\Tracking;

use Symfony\Component\EventDispatcher\GenericEvent;
use Synerise\Sdk\Exception\NotFoundException;
use Synerise\SyliusIntegrationPlugin\EventProcessor\CartStatusClearProcessor;

class CartStatusClearListener
{
    public function __construct(
        private CartStatusClearProcessor $processor
    ) {
    }

    /**
     * @throws NotFoundException
     */
    public function __invoke(GenericEvent $event): void
    {
        $this->processor->process();
    }
}
