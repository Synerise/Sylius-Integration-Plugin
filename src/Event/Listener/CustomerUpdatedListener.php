<?php

namespace Synerise\SyliusIntegrationPlugin\Event\Listener;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Synerise\SyliusIntegrationPlugin\Event\Processor\CustomerProcessorInterface;
use Webmozart\Assert\Assert;

final readonly class CustomerUpdatedListener
{
    public function __construct(
        private CustomerProcessorInterface $customerProcessor,
    )
    {
    }

    public function __invoke(GenericEvent $event): void
    {
        /** @var CustomerInterface $customer */
        $customer = $event->getSubject();
        Assert::isInstanceOf($customer, CustomerInterface::class);

        $this->customerProcessor->process($customer);
    }
}
