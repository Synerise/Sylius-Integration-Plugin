<?php

namespace Synerise\SyliusIntegrationPlugin\Event\Listener;

use Symfony\Component\EventDispatcher\GenericEvent;
use Synerise\Sdk\Exception\NotFoundException;
use Synerise\SyliusIntegrationPlugin\Event\Processor\CartStatusClearProcessor;

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
